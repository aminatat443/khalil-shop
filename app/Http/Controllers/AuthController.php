<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    /**
     * Connexion (section 37 du cahier des charges) — fenêtre flottante, pas de page dédiée.
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $message = "Email ou mot de passe incorrect.";

            if ($request->wantsJson()) {
                throw ValidationException::withMessages(['email' => $message]);
            }

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        // Seuls les clients inscrits par email doivent vérifier leur adresse avant de se
        // connecter (le staff est créé par un administrateur, Google vérifie déjà l'email).
        if (Auth::user()->role === Role::Client && ! Auth::user()->hasVerifiedEmail()) {
            $unverifiedUser = Auth::user();
            Auth::logout();

            $message = "Merci de vérifier votre adresse email avant de vous connecter. Consultez votre boîte de réception (et vos spams).";

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'unverified_email' => $unverifiedUser->email,
                ], 422);
            }

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->orders->syncGuestOrders(Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'name' => Auth::user()->name,
                'redirect' => url()->previous(),
            ]);
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Création de compte client (section 37 du cahier des charges) — même fenêtre flottante que
     * la connexion, avec un simple basculement entre les deux formulaires.
     */
    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => Role::Client,
        ]);

        // Le compte n'est pas connecté tant que l'email n'est pas vérifié (lien envoyé
        // par email, section 37 du cahier des charges). Un envoi impossible (SMTP non
        // configuré, etc.) ne doit jamais empêcher la création du compte.
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);
        }

        $message = "Compte créé ! Vérifiez votre boîte mail (".$user->email.") pour activer votre compte avant de vous connecter.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('home')->with('status', $message);
    }

    /**
     * Connexion Google (section 37 du cahier des charges — alternative à l'email/mot de passe).
     * Compte identifié/créé par email ; un mot de passe aléatoire est généré pour les nouveaux
     * comptes puisque le champ est obligatoire en base mais jamais utilisé pour ces connexions.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException|\Throwable $e) {
            return redirect()->route('home')->withErrors(['email' => 'La connexion avec Google a échoué. Réessayez.']);
        }

        $user = User::firstOrNew(['email' => $googleUser->getEmail()]);

        if (! $user->exists) {
            $user->name = $googleUser->getName() ?: $googleUser->getNickname() ?: 'Client KhalilShop';
            $user->password = Hash::make(Str::random(40));
            $user->role = Role::Client;
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user, true);

        $this->orders->syncGuestOrders($user);

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Lien de vérification cliqué depuis l'email (URL signée, section 37 du cahier des
     * charges) — le compte n'étant pas connecté à l'inscription, cette validation ne dépend
     * pas d'une session active (contrairement à EmailVerificationRequest par défaut).
     * La signature de l'URL est déjà vérifiée par le middleware "signed" de la route.
     */
    public function verifyEmail(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);

        // Moment marquant → une modale centrée plutôt que le bandeau générique
        // (voir components/email-verified-modal.blade.php).
        return redirect()->route('home')->with('email_verified_message', 'Votre adresse email est vérifiée, bienvenue !');
    }

    /**
     * Renvoie l'email de vérification — utile si le premier n'est jamais arrivé.
     */
    public function resendVerification(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $data['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Message identique que le compte existe ou non, pour ne pas révéler quels emails
        // sont déjà inscrits.
        $message = "Si un compte existe avec cette adresse, un nouvel email de vérification vient d'être envoyé.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('status', $message);
    }
}
