@extends('layouts.admin-modal')

@php($modalBack = route('admin.categories.index'))

@section('title', $category->exists ? 'Modifier la catégorie' : 'Nouvelle catégorie')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">
    {{ $category->exists ? 'Modifier « '.$category->name.' »' : 'Nouvelle catégorie' }}
</h1>

<form
    action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    method="POST"
    class="mt-8 space-y-6"
>
    @csrf
    @if($category->exists) @method('PUT') @endif

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Nom</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Catégorie parente</label>
        <select name="parent_id" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            <option value="">— Univers (aucun parent) —</option>
            @foreach($universes as $universe)
                <option value="{{ $universe->id }}" @selected(old('parent_id', $category->parent_id) == $universe->id)>{{ $universe->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Description (optionnel)</label>
        <textarea name="description" rows="3" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">{{ old('description', $category->description) }}</textarea>
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Image (URL, optionnel)</label>
        <input type="text" name="image" value="{{ old('image', $category->image) }}" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Ordre d'affichage</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>

    <label class="flex items-center gap-2.5 text-sm text-secondary-shade dark:text-white">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) class="h-4 w-4 text-primary focus:ring-primary">
        Catégorie active
    </label>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.categories.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary dark:text-white/70">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md">
            {{ $category->exists ? 'Enregistrer' : 'Créer la catégorie' }}
        </button>
    </div>
</form>

@if($category->exists)
    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="mt-6" onsubmit="return confirm('Supprimer cette catégorie ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.15em] text-primary hover:underline">
            Supprimer cette catégorie
        </button>
    </form>
@endif

@endsection
