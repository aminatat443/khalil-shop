@extends('layouts.app')

@section('title', 'Panier — KhalilShop')

@section('content')
<div x-data class="mx-auto max-w-3xl px-6 py-16 sm:px-10">

    <h1 class="font-display text-4xl font-normal italic text-secondary-shade">Mon panier</h1>

    <template x-if="$store.cart.items.length === 0">
        <div class="mt-16 flex flex-col items-center justify-center border border-secondary-shade/10 py-20 text-center">
            <i class="fa-solid fa-bag-shopping mb-4 text-2xl text-secondary-shade/20"></i>
            <p class="text-sm text-grey">Votre panier est vide.</p>
            <a href="{{ route('home') }}" class="mt-6 bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                Continuer mes achats
            </a>
        </div>
    </template>

    <template x-if="$store.cart.items.length > 0">
        <div>
            <div class="mt-10 divide-y divide-secondary-shade/10 border-t border-secondary-shade/10">
                <template x-for="item in $store.cart.items" :key="item.product_id + ':' + item.variant_id">
                    <div class="py-6">
                        <div class="flex items-center gap-5">
                            <a :href="item.url" class="h-24 w-20 shrink-0 overflow-hidden bg-grey-tint">
                                <img :src="item.image" :alt="item.name" class="h-full w-full object-cover">
                            </a>

                            <div class="flex-1">
                                <a :href="item.url" class="text-sm font-medium text-secondary-shade hover:text-primary" x-text="item.name"></a>
                                <template x-if="item.variant_label">
                                    <p class="mt-1 text-xs text-grey" x-text="item.variant_label"></p>
                                </template>
                                <p class="mt-1 text-xs text-grey" x-text="'Quantité : ' + item.quantity"></p>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <p class="text-sm font-medium text-secondary-shade" x-text="new Intl.NumberFormat('fr-FR').format(item.subtotal) + ' FCFA'"></p>
                                <button type="button" @click="$store.cart.remove(item.product_id, item.variant_id)" class="text-xs font-semibold uppercase tracking-[0.05em] text-primary hover:underline">
                                    Supprimer
                                </button>
                            </div>
                        </div>

                        {{-- Taille/couleur choisies ici, au moment de la commande, plutôt qu'à l'ajout au panier --}}
                        <template x-if="item.needs_variant">
                            <div x-data="{ colorId: null, sizeId: null }" class="mt-4 bg-primary-tint/40 p-4">
                                <p class="mb-3 text-xs font-medium text-primary-shade">
                                    <i class="fa-solid fa-circle-info mr-1.5"></i>Choisissez une taille et une couleur
                                </p>

                                <template x-if="item.variant_options.colors.length > 0">
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        <template x-for="color in item.variant_options.colors" :key="color.id">
                                            <button
                                                type="button"
                                                @click="colorId = color.id"
                                                :class="colorId === color.id ? 'ring-2 ring-offset-1 ring-secondary-shade' : 'ring-1 ring-secondary-shade/20'"
                                                class="h-7 w-7 rounded-full"
                                                :style="'background-color: ' + (color.hex || '#ccc')"
                                                :title="color.name"
                                            ></button>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="item.variant_options.sizes.length > 0">
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        <template x-for="size in item.variant_options.sizes" :key="size.id">
                                            <button
                                                type="button"
                                                @click="sizeId = size.id"
                                                :class="sizeId === size.id ? 'border-secondary-shade text-secondary-shade' : 'border-secondary-shade/20 text-secondary-shade'"
                                                class="border px-3 py-1.5 text-xs"
                                                x-text="size.name"
                                            ></button>
                                        </template>
                                    </div>
                                </template>

                                <button
                                    type="button"
                                    x-show="(item.variant_options.colors.length === 0 || colorId) && (item.variant_options.sizes.length === 0 || sizeId)"
                                    @click="
                                        const match = item.variant_options.variants.find(v =>
                                            (item.variant_options.colors.length === 0 || v.color_id === colorId) &&
                                            (item.variant_options.sizes.length === 0 || v.size_id === sizeId)
                                        );
                                        if (match) $store.cart.chooseVariant(item.product_id, match.id);
                                    "
                                    class="w-full bg-secondary-shade py-2.5 text-xs font-semibold uppercase tracking-[0.08em] text-white transition hover:bg-primary sm:w-auto sm:px-8"
                                >
                                    Valider
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="mt-8 flex items-center justify-between border-t border-secondary-shade/10 pt-6">
                <span class="text-sm font-medium uppercase tracking-[0.1em] text-secondary-shade">Sous-total</span>
                <span class="font-display text-2xl italic text-secondary-shade" x-text="new Intl.NumberFormat('fr-FR').format($store.cart.subtotal) + ' FCFA'"></span>
            </div>

            <a
                href="{{ route('checkout.index') }}"
                :class="$store.cart.items.some(i => i.needs_variant) ? 'pointer-events-none cursor-not-allowed bg-grey-tint text-grey/60' : 'bg-secondary-shade text-white hover:bg-primary'"
                class="mt-8 block px-6 py-4 text-center text-xs font-semibold uppercase tracking-[0.15em] transition"
            >
                Passer la commande
            </a>
            <template x-if="$store.cart.items.some(i => i.needs_variant)">
                <p class="mt-3 text-center text-xs text-grey">Choisissez une taille et une couleur pour chaque article avant de commander.</p>
            </template>
        </div>
    </template>

</div>
@endsection
