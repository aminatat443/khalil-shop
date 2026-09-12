@extends('layouts.admin')

@section('title', 'Catégories')

@section('content')

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Catégories</h1>
    @can('create', App\Models\Category::class)
        <a href="{{ route('admin.categories.create') }}" class="block bg-secondary-shade px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md sm:inline-block">
            Ajouter une catégorie
        </a>
    @endcan
</div>

{{-- Cartes (mobile/tablette) --}}
<div class="mt-8 space-y-3 lg:hidden">
    @forelse($categories as $universe)
        <div onclick="window.location='{{ route('admin.categories.edit', $universe) }}'" class="cursor-pointer bg-white p-4 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
            <div class="flex items-center justify-between gap-3">
                <span class="font-medium text-secondary-shade dark:text-white">{{ $universe->name }}</span>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="text-xs {{ $universe->is_active ? 'text-primary' : 'text-grey dark:text-white/40' }}">{{ $universe->is_active ? 'Active' : 'Désactivée' }}</span>
                    <a href="{{ route('admin.categories.edit', $universe) }}" title="Modifier" aria-label="Modifier" class="text-sm text-secondary-shade hover:text-primary dark:text-white/70"><i class="fa-solid fa-pen"></i></a>
                </div>
            </div>
        </div>
        @foreach($universe->children as $child)
            <div onclick="window.location='{{ route('admin.categories.edit', $child) }}'" class="ml-4 cursor-pointer bg-grey-tint/40 p-4 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-white/5 dark:ring-white/5">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-secondary-shade dark:text-white"><i class="fa-solid fa-turn-up fa-rotate-90 mr-2 text-[10px] text-grey dark:text-white/40"></i>{{ $child->name }}</span>
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="text-xs {{ $child->is_active ? 'text-primary' : 'text-grey dark:text-white/40' }}">{{ $child->is_active ? 'Active' : 'Désactivée' }}</span>
                        <a href="{{ route('admin.categories.edit', $child) }}" title="Modifier" aria-label="Modifier" class="text-sm text-secondary-shade hover:text-primary dark:text-white/70"><i class="fa-solid fa-pen"></i></a>
                    </div>
                </div>
            </div>
        @endforeach
    @empty
        <div class="bg-white px-6 py-10 text-center text-sm text-grey shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:text-white/40 dark:ring-white/5">Aucune catégorie pour le moment.</div>
    @endforelse
</div>

<div class="mt-8 hidden bg-white shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5 lg:block">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey dark:border-white/10 dark:text-white/40">
                <th class="px-6 py-4 font-medium">Nom</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($categories as $universe)
                <tr onclick="window.location='{{ route('admin.categories.edit', $universe) }}'" class="cursor-pointer transition hover:bg-grey-tint/40 dark:hover:bg-white/5">
                    <td class="px-6 py-4 font-medium text-secondary-shade dark:text-white">{{ $universe->name }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $universe->is_active ? 'text-primary' : 'text-grey dark:text-white/40' }}">{{ $universe->is_active ? 'Active' : 'Désactivée' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.categories.edit', $universe) }}" title="Modifier" aria-label="Modifier" class="inline-flex h-8 w-8 items-center justify-center text-sm text-secondary-shade hover:text-primary dark:text-white/70"><i class="fa-solid fa-pen"></i></a>
                    </td>
                </tr>
                @foreach($universe->children as $child)
                    <tr onclick="window.location='{{ route('admin.categories.edit', $child) }}'" class="cursor-pointer bg-grey-tint/40 transition hover:bg-grey-tint dark:bg-white/5 dark:hover:bg-white/10">
                        <td class="px-6 py-3 pl-12 text-secondary-shade dark:text-white">
                            <i class="fa-solid fa-turn-up fa-rotate-90 mr-2 text-[10px] text-grey dark:text-white/40"></i>{{ $child->name }}
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-xs {{ $child->is_active ? 'text-primary' : 'text-grey dark:text-white/40' }}">{{ $child->is_active ? 'Active' : 'Désactivée' }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $child) }}" title="Modifier" aria-label="Modifier" class="inline-flex h-8 w-8 items-center justify-center text-sm text-secondary-shade hover:text-primary dark:text-white/70"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-grey dark:text-white/40">Aucune catégorie pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
