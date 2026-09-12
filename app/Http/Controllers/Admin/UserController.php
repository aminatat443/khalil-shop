<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Vue d'ensemble de tous les comptes (clients + équipe) et de leur activité de connexion.
     * "En ligne" / "dernière activité" vient de la table `sessions` (driver database, section
     * — pas de connexion permanente à observer autrement) ; "dernière connexion" vient de
     * users.last_login_at, renseigné à chaque connexion réussie (voir AppServiceProvider).
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $lastActivity = DB::table('sessions')
            ->selectRaw('user_id, MAX(last_activity) as last_activity')
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $users = User::query()
            ->leftJoinSub($lastActivity, 'activity', 'activity.user_id', '=', 'users.id')
            ->select('users.*', 'activity.last_activity')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->input('q').'%';
                $query->where(fn ($q) => $q->where('users.name', 'ilike', $term)->orWhere('users.email', 'ilike', $term));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('users.role', $request->input('role')))
            ->orderByDesc('activity.last_activity')
            ->paginate(25)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.users.partials.table', ['users' => $users])->render(),
            ]);
        }

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Création manuelle d'un compte client depuis le back-office — les comptes équipe
     * (Gestionnaire/Admin) restent créés depuis la section "Équipe" (rôles distincts).
     */
    public function create(): View
    {
        $this->authorize('create', [User::class, Role::Client]);

        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', [User::class, Role::Client]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => Role::Client,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Compte créé.');
    }
}
