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

    <div class="grid gap-16 md:grid-cols-[220px_1fr]">

        {{-- Filtres (section 21) --}}
        <aside class="h-fit">
            <form method="GET" class="space-y-10">

                <div>
                    <h3 class="mb-4 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Prix (FCFA)</h3>
                    <div class="flex items-center gap-3">
                        <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min" class="w-full border-b border-secondary-shade/20 bg-transparent py-1.5 text-sm text-secondary-shade outline-none placeholder:text-grey/40 focus:border-primary">
                        <span class="text-grey/40">–</span>
                        <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max" class="w-full border-b border-secondary-shade/20 bg-transparent py-1.5 text-sm text-secondary-shade outline-none placeholder:text-grey/40 focus:border-primary">
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Filtres</h3>
                    <label class="flex items-center gap-2.5 text-sm text-secondary-shade">
                        <input type="checkbox" name="nouveautes" value="1" @checked(request('nouveautes')) class="h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                        Nouveautés
                    </label>
                    <label class="flex items-center gap-2.5 text-sm text-secondary-shade">
                        <input type="checkbox" name="promotions" value="1" @checked(request('promotions')) class="h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                        Promotions
                    </label>
                </div>

                <input type="hidden" name="tri" value="{{ request('tri') }}">

                <button type="submit" class="w-full bg-secondary-shade px-4 py-3.5 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                    Filtrer
                </button>
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
