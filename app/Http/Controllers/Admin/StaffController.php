<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Gestion de l'équipe (Gestionnaire / Administrateur) — docs/SPEC.md §2.5. Seul le Super
     * Administrateur crée un compte Administrateur ; un Administrateur crée un Gestionnaire.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.staff.index', [
            'staff' => User::whereIn('role', [Role::Gestionnaire, Role::Admin, Role::SuperAdmin])
                ->when($request->filled('q'), function ($query) use ($request) {
                    $term = '%'.$request->input('q').'%';
                    $query->where(fn ($q) => $q->where('name', 'ilike', $term)->orWhere('email', 'ilike', $term));
                })
                ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
                ->get(),
        ]);
    }

    public function create(): View
    {
        $assignableRoles = $this->assignableRoles();

        abort_if(empty($assignableRoles), 403);

        return view('admin.staff.form', ['assignableRoles' => $assignableRoles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:gestionnaire,admin'],
        ]);

        $role = Role::from($data['role']);

        $this->authorize('create', [User::class, $role]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            // Compte créé directement par un administrateur, pas d'auto-inscription :
            // pas besoin de vérification d'email comme pour les clients.
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')->with('status', 'Compte créé.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.staff.index')->with('status', 'Compte supprimé.');
    }

    /**
     * @return list<Role>
     */
    private function assignableRoles(): array
    {
        $user = auth()->user();
        $roles = [];

        if ($user->can('create', [User::class, Role::Gestionnaire])) {
            $roles[] = Role::Gestionnaire;
        }

        if ($user->can('create', [User::class, Role::Admin])) {
            $roles[] = Role::Admin;
        }

        return $roles;
    }
}
