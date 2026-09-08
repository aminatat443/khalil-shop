@extends('layouts.admin-modal')

@php($modalBack = route('admin.banners.index'))

@section('title', $banner->exists ? 'Modifier la bannière' : 'Nouvelle bannière')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">
    {{ $banner->exists ? 'Modifier la bannière' : 'Nouvelle bannière' }}
</h1>

<form
    action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="mt-8 space-y-6"
>
    @csrf
    @if($banner->exists) @method('PUT') @endif

    @if($banner->exists)
        <div class="aspect-video overflow-hidden bg-grey-tint">
            <img src="{{ $banner->image }}" alt="" class="h-full w-full object-cover">
        </div>
    @endif

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
            Image {{ $banner->exists ? '(laisser vide pour conserver l\'actuelle)' : '' }}
        </label>
        <input type="file" name="image_file" accept="image/*" {{ $banner->exists ? '' : 'required' }} class="w-full text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em]">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Emplacement</label>
        <select name="placement" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            @foreach(['hero' => 'Hero (accueil)', 'categorie' => 'Section catégorie', 'promo' => 'Section promotions'] as $value => $label)
                <option value="{{ $value }}" @selected(old('placement', $banner->placement) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Titre (optionnel)</label>
        <input type="text" name="title" value="{{ old('title', $banner->title) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Sous-titre (optionnel)</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Lien (optionnel)</label>
            <input type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="/femme" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Texte du bouton (optionnel)</label>
            <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Ordre d'affichage</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    </div>

    <label class="flex items-center gap-2.5 text-sm text-secondary-shade">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="h-4 w-4 text-primary focus:ring-primary">
        Bannière active
    </label>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.banners.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            {{ $banner->exists ? 'Enregistrer' : 'Créer la bannière' }}
        </button>
    </div>
</form>

@if($banner->exists)
    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="mt-6" onsubmit="return confirm('Supprimer cette bannière ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.15em] text-primary hover:underline">Supprimer cette bannière</button>
    </form>
@endif

@endsection
