@extends('layouts.admin-modal')

@php($modalBack = route('admin.promotions.index'))

@section('title', $promotion->exists ? 'Modifier la promotion' : 'Nouvelle promotion')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">
    {{ $promotion->exists ? 'Modifier la promotion' : 'Nouvelle promotion' }}
</h1>

<form
    action="{{ $promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}"
    method="POST"
    class="mt-8 space-y-6"
>
    @csrf
    @if($promotion->exists) @method('PUT') @endif

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Catégorie ciblée (optionnel)</label>
        <select name="category_id" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            <option value="">— Aucune —</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $promotion->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Produit ciblé (optionnel, prioritaire sur la catégorie)</label>
        <select name="product_id" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            <option value="">— Aucun —</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $promotion->product_id) == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Type</label>
            <select name="type" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
                <option value="percentage" @selected(old('type', $promotion->type) === 'percentage')>Pourcentage</option>
                <option value="fixed" @selected(old('type', $promotion->type) === 'fixed')>Montant fixe</option>
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Valeur</label>
            <input type="number" name="value" value="{{ old('value', $promotion->value) }}" required min="1" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Début (optionnel)</label>
            <input type="date" name="starts_at" value="{{ old('starts_at', $promotion->starts_at?->format('Y-m-d')) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Fin (optionnel)</label>
            <input type="date" name="ends_at" value="{{ old('ends_at', $promotion->ends_at?->format('Y-m-d')) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>
    </div>

    <label class="flex items-center gap-2.5 text-sm text-secondary-shade">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotion->is_active ?? true)) class="h-4 w-4 text-primary focus:ring-primary">
        Promotion active
    </label>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.promotions.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            {{ $promotion->exists ? 'Enregistrer' : 'Créer la promotion' }}
        </button>
    </div>
</form>

@if($promotion->exists)
    <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" class="mt-6" onsubmit="return confirm('Supprimer cette promotion ?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.15em] text-primary hover:underline">Supprimer cette promotion</button>
    </form>
@endif

@endsection
