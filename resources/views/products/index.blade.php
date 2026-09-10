@extends('layouts.app')

@section('title', $category->name.' — KhalilShop')

@section('content')

<div class="border-b border-secondary-shade/10">
    <nav class="mx-auto max-w-[1600px] px-6 py-5 text-xs uppercase tracking-[0.1em] text-grey sm:px-10">
        <a href="{{ route('home') }}" class="hover:text-primary">Accueil</a>
        <span class="mx-2">/</span>
        <span class="text-secondary-shade">{{ $category->name }}</span>
    </nav>
</div>

<div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">

    <div class="mb-16 flex flex-wrap items-end justify-between gap-4">
        <h1 class="font-display text-5xl font-normal italic text-secondary-shade">{{ $category->name }}</h1>
        <p class="text-xs uppercase tracking-[0.1em] text-grey">{{ $products->total() }} produit(s)</p>
    </div>

    <div class="grid gap-16 md:grid-cols-[260px_1fr]">

        {{-- Filtres (section 21) --}}
        <aside class="h-fit">
            <form
                method="GET"
                x-data="{ loading: false, active: {{ (request()->boolean('nouveautes') ? 1 : 0) + (request()->boolean('promotions') ? 1 : 0) + (request()->filled('prix_min') || request()->filled('prix_max') ? 1 : 0) }} }"
                @submit="loading = true"
                :class="loading && 'opacity-50'"
                class="space-y-8 border border-secondary-shade/10 bg-white p-6 shadow-sm transition-opacity dark:border-white/10 dark:bg-[#16201f]"
            >
                <div class="flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/80">
                        <i class="fa-solid fa-sliders text-primary"></i>Filtres
                    </h2>
                    <span x-show="active > 0" x-cloak x-text="active" class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white"></span>
                </div>

                @if($priceCeil > $priceFloor)
                    <div class="border-t border-secondary-shade/10 pt-6 dark:border-white/10">
                        <h3 class="mb-5 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">Prix</h3>
                        <x-price-range-slider :min="$priceFloor" :max="$priceCeil" :selected-min="request('prix_min')" :selected-max="request('prix_max')" />
                    </div>
                @endif

                <div class="space-y-3 border-t border-secondary-shade/10 pt-6 dark:border-white/10">
                    <h3 class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">Collection</h3>

                    <label class="group flex cursor-pointer items-center justify-between py-1">
                        <span class="flex items-center gap-2.5 text-sm text-secondary-shade dark:text-white/80">
                            <i class="fa-solid fa-sparkles text-xs text-grey/50 group-has-[:checked]:text-primary"></i>
                            Nouveautés
                        </span>
                        <input type="checkbox" name="nouveautes" value="1" @checked(request('nouveautes')) @change="$el.form.requestSubmit()" class="peer sr-only">
                        <span class="relative h-5 w-9 shrink-0 rounded-full bg-grey-tint transition peer-checked:bg-primary dark:bg-white/10">
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                        </span>
                    </label>
                    <label class="group flex cursor-pointer items-center justify-between py-1">
                        <span class="flex items-center gap-2.5 text-sm text-secondary-shade dark:text-white/80">
                            <i class="fa-solid fa-tag text-xs text-grey/50 group-has-[:checked]:text-primary"></i>
                            Promotions
                        </span>
                        <input type="checkbox" name="promotions" value="1" @checked(request('promotions')) @change="$el.form.requestSubmit()" class="peer sr-only">
                        <span class="relative h-5 w-9 shrink-0 rounded-full bg-grey-tint transition peer-checked:bg-primary dark:bg-white/10">
                            <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                        </span>
                    </label>
                </div>

                <input type="hidden" name="tri" value="{{ request('tri') }}">

                @if(request()->filled('prix_min') || request()->filled('prix_max') || request()->boolean('nouveautes') || request()->boolean('promotions'))
                    <a href="{{ route('catalog.show', $category) }}" class="block border-t border-secondary-shade/10 pt-5 text-center text-xs font-semibold uppercase tracking-[0.1em] text-grey transition hover:text-primary dark:border-white/10 dark:text-white/50">
                        Réinitialiser les filtres
                    </a>
                @endif

                <noscript>
                    <button type="submit" class="w-full bg-secondary-shade px-4 py-3.5 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                        Filtrer
                    </button>
                </noscript>
            </form>
        </aside>

        <div>

            {{-- Tri (section 22) --}}
            <div class="mb-10 flex justify-end">
                <form method="GET" x-data @change="$event.target.form.submit()">
                    @foreach(['prix_min', 'prix_max', 'nouveautes', 'promotions'] as $field)
                        @if(request()->filled($field))
                            <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                        @endif
                    @endforeach

                    <select name="tri" class="border-b border-secondary-shade/20 bg-transparent py-1.5 text-sm text-secondary-shade outline-none focus:border-primary">
                        <option value="pertinence" @selected(request('tri', 'pertinence') === 'pertinence')>Pertinence</option>
                        <option value="nouveautes" @selected(request('tri') === 'nouveautes')>Nouveautés</option>
                        <option value="prix_croissant" @selected(request('tri') === 'prix_croissant')>Prix croissant</option>
                        <option value="prix_decroissant" @selected(request('tri') === 'prix_decroissant')>Prix décroissant</option>
                    </select>
                </form>
            </div>

            {{-- Grille (section 20 : 2 col mobile / 3 tablette / 4 desktop) --}}
            @if($products->isEmpty())
                <div class="flex flex-col items-center justify-center border border-secondary-shade/10 py-24 text-center">
                    <i class="fa-solid fa-basket-shopping mb-4 text-2xl text-secondary-shade/20"></i>
                    <p class="text-sm text-grey">Aucun produit ne correspond à ces critères pour le moment.</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-x-6 gap-y-14 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $products->links() }}
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
