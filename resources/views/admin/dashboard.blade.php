@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Tableau de bord</h1>

{{-- Indicateurs clés (section 40) --}}
<div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="bg-white p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Chiffre d'affaires</p>
        <p class="mt-2 font-display text-2xl italic text-secondary-shade">{{ number_format($revenue, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Commandes</p>
        <p class="mt-2 font-display text-2xl italic text-secondary-shade">{{ $ordersCount }}</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Clients</p>
        <p class="mt-2 font-display text-2xl italic text-secondary-shade">{{ $clientsCount }}</p>
    </div>
    <div class="bg-white p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Produits</p>
        <p class="mt-2 font-display text-2xl italic text-secondary-shade">{{ $productsCount }}</p>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">

    {{-- Ventes récentes --}}
    <div class="bg-white p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Ventes récentes</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary hover:underline">Tout voir</a>
        </div>

        <div class="mt-4 divide-y divide-secondary-shade/10">
            @forelse($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between py-3 text-sm hover:text-primary">
                    <span>
                        <span class="font-medium">{{ $order->order_number }}</span>
                        <span class="ml-2 text-xs text-grey">{{ $order->customer_name }}</span>
                    </span>
                    <span class="text-xs font-medium">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </a>
            @empty
                <p class="py-3 text-sm text-grey">Aucune commande pour le moment.</p>
            @endforelse
        </div>
    </div>

    {{-- Stock faible --}}
    <div class="bg-white p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Stock faible</h2>
            <span class="text-xs text-grey">{{ $outOfStockCount }} en rupture</span>
        </div>

        <div class="mt-4 divide-y divide-secondary-shade/10">
            @forelse($lowStockVariants as $variant)
                <a href="{{ route('admin.products.edit', $variant->product) }}" class="flex items-center justify-between py-3 text-sm hover:text-primary">
                    <span>{{ $variant->product->name }} <span class="text-xs text-grey">#{{ $variant->sku }}</span></span>
                    <span class="text-xs font-medium text-primary">{{ $variant->stock }} restant(s)</span>
                </a>
            @empty
                <p class="py-3 text-sm text-grey">Aucune alerte de stock faible.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Produits populaires --}}
<div class="mt-6 bg-white p-6">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Produits populaires</h2>

    <div class="mt-4 divide-y divide-secondary-shade/10">
        @forelse($popularProducts as $product)
            <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center justify-between py-3 text-sm hover:text-primary">
                <span>{{ $product->name }}</span>
                <span class="text-xs text-grey">{{ $product->total_sold ?? 0 }} vendu(s)</span>
            </a>
        @empty
            <p class="py-3 text-sm text-grey">Pas encore de ventes.</p>
        @endforelse
    </div>
</div>

@endsection
