@extends('layouts.admin-modal')

@php($modalBack = route('admin.users.index'))

@section('title', 'Créer un compte')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Créer un compte client</h1>
<p class="mt-2 text-sm text-grey dark:text-white/40">Le compte est créé avec l'email déjà vérifié — le client peut se connecter immédiatement.</p>

<form action="{{ route('admin.users.store') }}" method="POST" class="mt-8 space-y-6">
    @csrf

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Nom complet</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Mot de passe</label>
            <input type="password" name="password" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Confirmer</label>
            <input type="password" name="password_confirmation" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>
    </div>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.users.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary dark:text-white/70">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md">
            Créer le compte
        </button>
    </div>
</form>

@endsection
