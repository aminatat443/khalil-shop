@props(['product'])

@php
    $price = number_format($product->price, 0, ',', ' ');
    $oldPrice = $product->old_price ? number_format($product->old_price, 0, ',', ' ') : null;
    $discount = $product->old_price ? (int) round((1 - $product->price / $product->old_price) * 100) : null;
    $image = img_url($product->images->first()?->url, 500, 625);
@endphp

<div x-data class="group relative">

    {{-- Image + badges + favori --}}
    <div class="relative aspect-[4/5] overflow-hidden bg-grey-tint">

        <a href="{{ route('products.show', $product) }}" class="absolute inset-0 z-0">
            @if($image)
                <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 ease-out group-hover:scale-[1.04]">
            @endif
        </a>

        {{-- Réduction, à droite --}}
        @if($discount)
            <span class="pointer-events-none absolute right-3 top-3 z-10 bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-primary">
                -{{ $discount }}%
            </span>
        @endif

        {{-- Favori --}}
        <button
            type="button"
            @click="$store.favorites.toggle({ id: {{ $product->id }}, name: @js($product->name), price: {{ $product->price }}, image: @js($image), url: @js(route('products.show', $product)) })"
            aria-label="Ajouter aux favoris"
            class="absolute right-3 z-10 text-secondary-shade drop-shadow-[0_1px_3px_rgba(0,0,0,0.25)] transition hover:scale-110 hover:text-primary {{ $discount ? 'top-12' : 'top-3' }}"
        >
            <i :class="$store.favorites.isFavorite({{ $product->id }}) ? 'fa-solid text-primary' : 'fa-regular'" class="fa-heart text-[16px]"></i>
        </button>

        {{-- Ajout direct au panier au survol, sans variante — le client choisit la taille/couleur
             au moment de la commande plutôt qu'ici (dans le panier, avant validation). --}}
        <form
            action="{{ route('cart.add') }}"
            method="POST"
            @submit.prevent="$store.cart.add($el)"
            class="absolute inset-x-0 bottom-0 z-10 translate-y-full opacity-0 transition duration-300 ease-out group-hover:translate-y-0 group-hover:opacity-100"
        >
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 bg-secondary-shade py-3 text-xs font-semibold uppercase tracking-[0.08em] text-white transition hover:bg-primary"
            >
                Ajouter au panier
            </button>
        </form>

    </div>

    {{-- Infos --}}
    <a href="{{ route('products.show', $product) }}" class="mt-3.5 block">
        <h3 class="truncate text-[13px] text-secondary-shade transition-colors group-hover:text-primary">{{ $product->name }}</h3>

        <div class="mt-1.5 flex items-baseline gap-2">
            <span class="text-sm font-semibold text-secondary-shade">{{ $price }} FCFA</span>
            @if($oldPrice)
                <span class="text-xs text-grey/50 line-through">{{ $oldPrice }} FCFA</span>
            @endif
        </div>
    </a>

</div>
