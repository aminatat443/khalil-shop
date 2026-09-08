@extends('layouts.admin')

@section('title', 'Équipe')

@section('content')

@php
    $roleLabels = ['super_admin' => 'Super Administrateur', 'admin' => 'Administrateur', 'gestionnaire' => 'Gestionnaire'];
@endphp

<div class="flex items-center justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Équipe</h1>
    <a href="{{ route('admin.staff.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
        Ajouter un compte
    </a>
</div>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom ou email…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-64 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Email</th>
                <th class="px-6 py-4 font-medium">Rôle</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @foreach($staff as $member)
                <tr>
                    <td class="px-6 py-4 font-medium text-secondary-shade">{{ $member->name }}</td>
                    <td class="px-6 py-4 text-grey">{{ $member->email }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ $roleLabels[$member->role->value] ?? $member->role->value }}</td>
                    <td class="px-6 py-4 text-right">
                        @can('delete', $member)
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-primary hover:underline">Supprimer</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
