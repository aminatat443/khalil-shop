@extends('layouts.admin')

@section('title', 'Catégories')

@section('content')

<div class="flex items-center justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Catégories</h1>
    @can('create', App\Models\Category::class)
        <a href="{{ route('admin.categories.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Ajouter une catégorie
        </a>
    @endcan
</div>

<div class="mt-8 bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($categories as $universe)
                <tr onclick="window.location='{{ route('admin.categories.edit', $universe) }}'" class="cursor-pointer transition hover:bg-grey-tint/40">
                    <td class="px-6 py-4 font-medium text-secondary-shade">{{ $universe->name }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $universe->is_active ? 'text-primary' : 'text-grey' }}">{{ $universe->is_active ? 'Active' : 'Désactivée' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.categories.edit', $universe) }}" class="text-xs text-secondary-shade hover:text-primary">Modifier</a>
                    </td>
                </tr>
                @foreach($universe->children as $child)
                    <tr onclick="window.location='{{ route('admin.categories.edit', $child) }}'" class="cursor-pointer bg-grey-tint/40 transition hover:bg-grey-tint">
                        <td class="px-6 py-3 pl-12 text-secondary-shade">
                            <i class="fa-solid fa-turn-up fa-rotate-90 mr-2 text-[10px] text-grey"></i>{{ $child->name }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs {{ $child->is_active ? 'text-primary' : 'text-grey' }}">{{ $child->is_active ? 'Active' : 'Désactivée' }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $child) }}" class="text-xs text-secondary-shade hover:text-primary">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-grey">Aucune catégorie pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
