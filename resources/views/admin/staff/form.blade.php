@extends('layouts.admin-modal')

@php($modalBack = route('admin.staff.index'))

@section('title', 'Nouveau compte')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Nouveau compte</h1>

<form action="{{ route('admin.staff.store') }}" method="POST" class="mt-8 space-y-6">
    @csrf

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Nom complet</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Mot de passe</label>
        <input type="password" name="password" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Rôle</label>
        <select name="role" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            @foreach($assignableRoles as $role)
                <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                    {{ $role === App\Enums\Role::Admin ? 'Administrateur' : 'Gestionnaire' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.staff.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">Créer le compte</button>
    </div>
</form>

@endsection
