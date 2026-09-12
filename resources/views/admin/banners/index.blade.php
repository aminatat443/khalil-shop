@extends('layouts.admin')

@section('title', 'Accueil / Bannières')

@section('content')

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Accueil & bannières</h1>
    @can('create', App\Models\Banner::class)
        <a href="{{ route('admin.banners.create') }}" class="block bg-secondary-shade px-6 py-3 text-center text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md sm:inline-block">
            Nouvelle bannière
        </a>
    @endcan
</div>

<div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
    @forelse($banners as $banner)
        <a href="{{ route('admin.banners.edit', $banner) }}" class="group block overflow-hidden bg-white shadow-sm ring-1 ring-secondary-shade/5 transition hover:shadow-md dark:bg-[#16201f] dark:ring-white/5">
            <div class="aspect-video overflow-hidden bg-grey-tint dark:bg-white/10">
                <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
            </div>
            <div class="p-4">
                <p class="text-xs uppercase tracking-[0.1em] text-grey dark:text-white/40">{{ $banner->placement }}</p>
                <p class="mt-1 text-sm font-medium text-secondary-shade group-hover:text-primary dark:text-white">{{ $banner->title ?? 'Sans titre' }}</p>
                <span class="mt-1 block text-xs {{ $banner->is_active ? 'text-primary' : 'text-grey dark:text-white/40' }}">{{ $banner->is_active ? 'Active' : 'Désactivée' }}</span>
            </div>
        </a>
    @empty
        <p class="col-span-full py-10 text-center text-grey dark:text-white/40">Aucune bannière pour le moment.</p>
    @endforelse
</div>

@endsection
