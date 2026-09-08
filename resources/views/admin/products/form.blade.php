@extends('layouts.admin-modal')

@php($modalBack = route('admin.products.index'))

@section('title', $product->exists ? 'Modifier le produit' : 'Nouveau produit')

@section('modal-width', 'max-w-3xl')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">
    {{ $product->exists ? 'Modifier « '.$product->name.' »' : 'Nouveau produit' }}
</h1>

{{-- Champs de base (section 42 du cahier des charges) --}}
<form
    action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
    method="POST"
    class="mt-8 space-y-6"
>
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="grid grid-cols-2 gap-6">
        <div class="col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Nom</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>

        <div class="col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Catégorie</label>
            <select name="category_id" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
                <option value="">— Choisir —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->parent?->name }} — {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Prix (FCFA)</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Ancien prix (optionnel)</label>
            <input type="number" name="old_price" value="{{ old('old_price', $product->old_price) }}" min="0" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
                Stock
                @if($product->variants->isNotEmpty() ?? false)
                    <span class="normal-case text-grey">(ignoré — géré par variante ci-dessous)</span>
                @endif
            </label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Matière (optionnel)</label>
            <input type="text" name="material" value="{{ old('material', $product->material) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        </div>

        <div class="col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Description</label>
            <textarea name="description" rows="4" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">{{ old('description', $product->description) }}</textarea>
        </div>
    </div>

    <div class="flex flex-wrap gap-x-8 gap-y-3 pt-2">
        @foreach(['is_new' => 'Nouveauté', 'is_promo' => 'Promotion', 'is_featured' => 'Produit vedette', 'is_active' => 'Actif'] as $field => $label)
            <label class="flex items-center gap-2.5 text-sm text-secondary-shade">
                <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $product->{$field} ?? ($field === 'is_active'))) class="h-4 w-4 text-primary focus:ring-primary">
                {{ $label }}
            </label>
        @endforeach
    </div>

    <div class="flex gap-4 pt-2">
        <a href="{{ route('admin.products.index') }}" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Annuler</a>
        <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            {{ $product->exists ? 'Enregistrer' : 'Créer le produit' }}
        </button>
    </div>
</form>

@if($product->exists)

    @can('delete', $product)
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="mt-4" onsubmit="return confirm('Supprimer définitivement ce produit ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold uppercase tracking-[0.15em] text-primary hover:underline">Supprimer ce produit</button>
        </form>
    @endcan

    {{-- Images (section 49 du cahier des charges) --}}
    <div class="mt-10 border-t border-secondary-shade/10 pt-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Images</h2>

        <div class="mt-5 grid grid-cols-4 gap-4">
            @foreach($product->images as $image)
                <div class="group relative aspect-square overflow-hidden bg-grey-tint">
                    <img src="{{ $image->url }}" alt="{{ $image->alt }}" class="h-full w-full object-cover">
                    <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" class="absolute inset-0 flex items-center justify-center bg-secondary-shade/60 opacity-0 transition group-hover:opacity-100" onsubmit="return confirm('Supprimer cette image ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-white" aria-label="Supprimer">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data" class="mt-6 flex flex-wrap items-center gap-4">
            @csrf
            <input type="file" name="image" accept="image/*" required class="text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em]">
            <button type="submit" class="bg-secondary-shade px-6 py-2.5 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">Ajouter</button>
        </form>
        <p class="mt-2 text-xs text-grey">Stockage local pour l'instant — l'intégration Cloudinary sera branchée ici (section 49 du cahier des charges).</p>
    </div>

    {{-- Variantes (sections 27, 41, 45 du cahier des charges) --}}
    <div class="mt-8 border-t border-secondary-shade/10 pt-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Variantes (taille / couleur / stock)</h2>

        <div class="mt-5 divide-y divide-secondary-shade/10">
            @forelse($product->variants as $variant)
                <div class="flex items-center gap-4 py-3 text-sm">
                    <span class="flex-1 text-secondary-shade">
                        {{ $variant->color?->name ?? '—' }} / {{ $variant->size?->name ?? '—' }}
                        <span class="text-xs text-grey">SKU {{ $variant->sku }}</span>
                    </span>
                    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="color_id" value="{{ $variant->color_id }}">
                        <input type="hidden" name="size_id" value="{{ $variant->size_id }}">
                        <input type="hidden" name="sku" value="{{ $variant->sku }}">
                        <input type="number" name="stock" value="{{ $variant->stock }}" min="0" class="w-20 border-b border-secondary-shade/20 bg-transparent py-1 text-sm outline-none focus:border-primary">
                        <button type="submit" class="text-xs text-secondary-shade hover:text-primary">Mettre à jour</button>
                    </form>
                    <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" onsubmit="return confirm('Supprimer cette variante ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-primary hover:underline">Supprimer</button>
                    </form>
                </div>
            @empty
                <p class="py-3 text-sm text-grey">Aucune variante — le champ "Stock" ci-dessus s'applique directement au produit.</p>
            @endforelse
        </div>

        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-5">
            @csrf
            <div x-data="{ colorId: '', colorHex: '', colorName: '', colorOpen: false }" @click.outside="colorOpen = false" class="relative">
                <input type="hidden" name="color_id" :value="colorId">
                <button
                    type="button"
                    @click="colorOpen = !colorOpen"
                    class="flex w-full items-center gap-2 border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary"
                >
                    <span class="h-4 w-4 shrink-0 rounded-full border border-secondary-shade/20" :style="colorHex ? ('background-color:' + colorHex) : ''"></span>
                    <span class="flex-1 truncate text-left" x-text="colorName || 'Couleur'"></span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-secondary-shade/40"></i>
                </button>

                <div
                    x-show="colorOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                    class="absolute left-0 top-full z-10 mt-2 w-48 border border-secondary-shade/10 bg-white p-3 shadow-[0_24px_40px_-24px_rgba(33,55,55,0.18)]"
                >
                    @forelse($colors as $color)
                        <button
                            type="button"
                            @click="colorId = '{{ $color->id }}'; colorHex = '{{ $color->hex_code ?? '#ccc' }}'; colorName = '{{ addslashes($color->name) }}'; colorOpen = false"
                            class="mb-1.5 mr-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full transition"
                            :class="colorId === '{{ $color->id }}' ? 'ring-2 ring-offset-2 ring-secondary-shade' : 'ring-1 ring-secondary-shade/15 hover:ring-secondary-shade/40'"
                            style="background-color: {{ $color->hex_code ?? '#ccc' }}"
                            title="{{ $color->name }}"
                            aria-label="{{ $color->name }}"
                        ></button>
                    @empty
                        <p class="text-xs text-grey">Aucune couleur enregistrée.</p>
                    @endforelse
                </div>
            </div>
            <select name="size_id" class="border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
                <option value="">Taille</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
            <input type="number" name="stock" placeholder="Stock" min="0" required class="border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            <input type="text" name="sku" placeholder="SKU" required class="border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
            <button type="submit" class="bg-secondary-shade px-4 py-2 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-primary">Ajouter</button>
        </form>
    </div>

@endif

@endsection
