@extends('layouts.app')

@section('title', ($query ? 'Résultats pour « '.$query.' »' : 'Rechercher').' — KhalilShop')

@section('content')
<div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">

    <h1 class="font-display text-4xl font-normal italic text-secondary-shade">
        @if($query)
            Résultats pour « {{ $query }} »
        @else
            Nos produits
        @endif
        <span class="font-sans text-sm not-italic text-grey">({{ $products->total() }})</span>
    </h1>

    @if($products->isEmpty())
        <div class="mt-16 flex flex-col items-center justify-center border border-secondary-shade/10 py-24 text-center">
            <i class="fa-solid fa-magnifying-glass mb-4 text-2xl text-secondary-shade/20"></i>
            <p class="text-sm text-grey">Aucun produit ne correspond à cette recherche.</p>
        </div>
    @else
        <div class="mt-16 grid grid-cols-2 gap-x-6 gap-y-14 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-16">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
