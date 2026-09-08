@extends('layouts.app')

@section('title', $product->name.' — KhalilShop')

@section('content')

<div class="border-b border-secondary-shade/10">
    <nav class="mx-auto max-w-[1600px] px-6 py-5 text-xs uppercase tracking-[0.1em] text-grey sm:px-10">
        <a href="{{ route('home') }}" class="hover:text-primary">Accueil</a>
        <span class="mx-2">/</span>
        <a href="{{ route('catalog.show', $product->category) }}" class="hover:text-primary">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-secondary-shade">{{ $product->name }}</span>
    </nav>
</div>

<div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">

    <div
        x-data="{
            colorId: null,
            sizeId: null,
            variants: {{ $product->variants->map(fn ($v) => ['id' => $v->id, 'color_id' => $v->color_id, 'size_id' => $v->size_id, 'stock' => $v->stock])->toJson() }},
            get selectedVariant() {
                return this.variants.find(v => v.color_id === this.colorId && v.size_id === this.sizeId) ?? null;
            },
            get canAddToCart() {
                return {{ $colors->isEmpty() && $sizes->isEmpty() ? 'true' : 'false' }} || (this.selectedVariant && this.selectedVariant.stock > 0);
            },
        }"
        class="grid gap-20 md:grid-cols-2"
    >
        {{-- Galerie (sections 25-26) --}}
        <div class="space-y-3">
            <div class="aspect-[4/5] overflow-hidden bg-grey-tint">
                @if($image = $product->images->first()?->url)
                    <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach($product->images->skip(1) as $img)
                        <div class="aspect-square overflow-hidden bg-grey-tint">
                            <img src="{{ $img->url }}" alt="{{ $img->alt ?? $product->name }}" class="h-full w-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Infos produit --}}
        <div class="md:pt-4">
            @if($product->is_new || $product->is_promo)
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.2em] {{ $product->is_promo ? 'text-primary' : 'text-secondary-shade' }}">
                    {{ $product->is_promo ? 'Promo' : 'Nouveau' }}
                </p>
            @endif

            <h1 class="font-display text-4xl font-normal italic text-secondary-shade">{{ $product->name }}</h1>

            <div class="mt-5 flex items-baseline gap-3">
                <p class="text-xl font-medium text-secondary-shade">{{ number_format($effectivePrice, 0, ',', ' ') }} FCFA</p>
                @if($product->old_price)
                    <p class="text-sm text-grey/50 line-through">{{ number_format($product->old_price, 0, ',', ' ') }} FCFA</p>
                @endif
            </div>

            @if($product->description)
                <p class="mt-6 max-w-md text-sm leading-6 text-grey">{{ $product->description }}</p>
            @endif

            <form action="{{ route('cart.add') }}" method="POST" @submit.prevent="$store.cart.add($el)" class="mt-10 space-y-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Couleurs (section 27) --}}
                @if($colors->isNotEmpty())
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-grey">Couleur</p>
                        <div class="flex gap-3">
                            @foreach($colors as $color)
                                <button
                                    type="button"
                                    @click="colorId = {{ $color->id }}"
                                    :class="colorId === {{ $color->id }} ? 'ring-1 ring-offset-2 ring-secondary-shade' : 'ring-1 ring-secondary-shade/15'"
                                    class="h-9 w-9 rounded-full transition"
                                    style="background-color: {{ $color->hex_code ?? '#ccc' }}"
                                    title="{{ $color->name }}"
                                ></button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tailles avec désactivation intelligente (section 28) --}}
                @if($sizes->isNotEmpty())
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-grey">Taille</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $size)
                                <button
                                    type="button"
                                    @click="sizeId = {{ $size->id }}"
                                    :class="sizeId === {{ $size->id }} ? 'border-secondary-shade text-secondary-shade' : 'border-secondary-shade/15 text-secondary-shade hover:border-secondary-shade/40'"
                                    class="border px-4 py-2 text-sm transition"
                                >
                                    {{ $size->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <input type="hidden" name="variant_id" x-model="selectedVariant ? selectedVariant.id : ''">

                <template x-if="selectedVariant && selectedVariant.stock <= 0">
                    <p class="text-sm font-medium text-primary-shade">Rupture de stock pour cette variante.</p>
                </template>

                <button
                    type="submit"
                    :disabled="!canAddToCart"
                    :class="canAddToCart ? 'bg-secondary-shade hover:bg-primary text-white' : 'cursor-not-allowed bg-grey-tint text-grey/60'"
                    class="w-full py-4 text-xs font-semibold uppercase tracking-[0.15em] transition"
                >
                    <span x-show="canAddToCart">Ajouter au panier</span>
                    <span x-show="!canAddToCart">{{ $inStock ? 'Sélectionnez une variante' : 'Rupture de stock' }}</span>
                </button>
            </form>

            <ul class="mt-12 grid grid-cols-2 gap-y-3 text-xs text-grey">
                <li class="flex items-center gap-2.5">
                    <i class="fa-solid fa-certificate text-secondary-shade/40"></i>
                    Produit garanti
                </li>
                <li class="flex items-center gap-2.5">
                    <i class="fa-solid fa-truck text-secondary-shade/40"></i>
                    Livraison Sénégal
                </li>
                <li class="flex items-center gap-2.5">
                    <i class="fa-solid fa-lock text-secondary-shade/40"></i>
                    Paiement sécurisé
                </li>
                <li class="flex items-center gap-2.5">
                    <i class="fa-solid fa-headset text-secondary-shade/40"></i>
                    Service client
                </li>
            </ul>
        </div>
    </div>

    {{-- Avis clients (section 39 du cahier des charges) --}}
    <div class="mt-20 max-w-2xl border-t border-secondary-shade/10 pt-12">
        <div class="flex flex-wrap items-center gap-4">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Avis clients</h2>
            @if($averageRating)
                <span class="flex items-center gap-1.5 text-sm text-secondary-shade">
                    <span class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star text-[11px] {{ $i <= round($averageRating) ? 'text-primary' : 'text-grey-tint' }}"></i>
                        @endfor
                    </span>
                    {{ $averageRating }} / 5 ({{ $reviews->count() }})
                </span>
            @endif
        </div>

        <div class="mt-6 divide-y divide-secondary-shade/10">
            @forelse($reviews as $review)
                <div class="py-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-secondary-shade">{{ $review->user->name }}</p>
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-[10px] {{ $i <= $review->rating ? 'text-primary' : 'text-grey-tint' }}"></i>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="mt-2 text-sm text-grey">{{ $review->comment }}</p>
                    @endif
                </div>
            @empty
                <p class="py-5 text-sm text-grey">Aucun avis pour le moment. Soyez le premier à donner votre avis.</p>
            @endforelse
        </div>

        @auth
            <div x-data="{ rating: {{ $myReview->rating ?? 0 }} }" class="mt-8 border-t border-secondary-shade/10 pt-8">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
                    {{ $myReview ? 'Modifier mon avis' : 'Laisser un avis' }}
                </p>

                <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="flex gap-1">
                        <template x-for="i in 5" :key="i">
                            <button type="button" @click="rating = i" class="text-lg transition" :class="i <= rating ? 'text-primary' : 'text-grey-tint'">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating">
                    <textarea name="comment" rows="3" placeholder="Votre commentaire (optionnel)" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                    <button type="submit" :disabled="rating === 0" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary disabled:cursor-not-allowed disabled:opacity-40">
                        Envoyer mon avis
                    </button>
                </form>

                @if($myReview && ! $myReview->is_approved)
                    <p class="mt-3 text-xs text-grey">Votre avis est en attente de modération.</p>
                @endif
            </div>
        @else
            <p class="mt-8 border-t border-secondary-shade/10 pt-8 text-sm text-grey">
                <button type="button" @click="$store.ui.openLogin()" class="font-medium text-secondary-shade hover:text-primary">Connectez-vous</button>
                pour laisser un avis.
            </p>
        @endauth
    </div>

</div>
@endsection
