@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Tableau de bord</h1>
        <p class="mt-1 text-sm text-grey dark:text-white/40">{{ now()->translatedFormat('l d F Y') }} — bon retour {{ auth()->user()->name }}.</p>
    </div>
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:text-primary dark:text-white/60 dark:hover:text-white">
        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>Voir la boutique
    </a>
</div>

{{-- Indicateurs clés (section 40) --}}
<div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @php
        $kpis = [
            ['icon' => 'fa-sack-dollar', 'label' => "Chiffre d'affaires", 'value' => number_format($revenue, 0, ',', ' ').' FCFA', 'tone' => 'primary'],
            ['icon' => 'fa-bag-shopping', 'label' => 'Commandes', 'value' => $ordersCount, 'tone' => 'blue', 'trend' => $ordersTrend],
            ['icon' => 'fa-users', 'label' => 'Clients', 'value' => $clientsCount, 'tone' => 'green'],
            ['icon' => 'fa-shirt', 'label' => 'Produits', 'value' => $productsCount, 'tone' => 'amber'],
        ];
        $toneClasses = [
            'primary' => 'bg-primary-tint text-primary-shade dark:bg-primary/15 dark:text-primary',
            'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
            'green' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
        ];
    @endphp

    @foreach($kpis as $kpi)
        <div class="group bg-white p-6 shadow-sm transition hover:shadow-md dark:bg-[#16201f]">
            <div class="flex items-center justify-between">
                <span class="flex h-10 w-10 items-center justify-center {{ $toneClasses[$kpi['tone']] }}">
                    <i class="fa-solid {{ $kpi['icon'] }} text-sm"></i>
                </span>
                @isset($kpi['trend'])
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold {{ $kpi['trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        <i class="fa-solid {{ $kpi['trend'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($kpi['trend']) }}%
                    </span>
                @endisset
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">{{ $kpi['label'] }}</p>
            <p class="mt-1 font-display text-2xl italic text-secondary-shade dark:text-white">{{ $kpi['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1.3fr_1fr]">

    {{-- Chiffre d'affaires — 7 derniers jours --}}
    <div class="bg-white p-6 shadow-sm dark:bg-[#16201f]">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Chiffre d'affaires — 7 derniers jours</h2>

        @php($maxRevenue = max($last7Days->max('revenue'), 1))
        <div class="mt-6 flex h-36 items-end gap-3">
            @foreach($last7Days as $day)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div class="flex h-28 w-full items-end">
                        <div
                            class="w-full bg-primary/20 transition hover:bg-primary/40 dark:bg-primary/25 dark:hover:bg-primary/50"
                            style="height: {{ $day['revenue'] > 0 ? max(6, round(($day['revenue'] / $maxRevenue) * 100)) : 2 }}%"
                            title="{{ number_format($day['revenue'], 0, ',', ' ') }} FCFA"
                        ></div>
                    </div>
                    <span class="text-[10px] uppercase text-grey dark:text-white/40">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Alertes rapides --}}
    <div class="grid grid-cols-2 gap-4">
        <a href="{{ route('admin.returns.index') }}" class="flex flex-col justify-between bg-white p-6 shadow-sm transition hover:shadow-md dark:bg-[#16201f]">
            <span class="flex h-10 w-10 items-center justify-center bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                <i class="fa-solid fa-rotate-left text-sm"></i>
            </span>
            <div class="mt-4">
                <p class="font-display text-2xl italic text-secondary-shade dark:text-white">{{ $pendingReturnsCount }}</p>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-grey dark:text-white/40">Retour(s) en attente</p>
            </div>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex flex-col justify-between bg-white p-6 shadow-sm transition hover:shadow-md dark:bg-[#16201f]">
            <span class="flex h-10 w-10 items-center justify-center bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
            </span>
            <div class="mt-4">
                <p class="font-display text-2xl italic text-secondary-shade dark:text-white">{{ $outOfStockCount }}</p>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-grey dark:text-white/40">Rupture(s) de stock</p>
            </div>
        </a>
    </div>

</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">

    {{-- Ventes récentes --}}
    <div class="bg-white p-6 shadow-sm dark:bg-[#16201f]">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Ventes récentes</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary hover:underline">Tout voir</a>
        </div>

        <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($recentOrders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between gap-3 py-3 text-sm transition hover:text-primary">
                    <span class="min-w-0">
                        <span class="font-medium text-secondary-shade dark:text-white">{{ $order->order_number }}</span>
                        <span class="ml-2 truncate text-xs text-grey dark:text-white/40">{{ $order->customer_name }}</span>
                    </span>
                    <span class="flex shrink-0 items-center gap-3">
                        <x-status-pill :tone="\App\Models\Order::STATUS_TONES[$order->status] ?? 'neutral'" :label="\App\Models\Order::STATUS_LABELS[$order->status] ?? $order->status" />
                        <span class="text-xs font-medium text-secondary-shade dark:text-white">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                    </span>
                </a>
            @empty
                <p class="py-3 text-sm text-grey dark:text-white/40">Aucune commande pour le moment.</p>
            @endforelse
        </div>
    </div>

    {{-- Stock faible --}}
    <div class="bg-white p-6 shadow-sm dark:bg-[#16201f]">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Stock faible</h2>
            <span class="text-xs text-grey dark:text-white/40">{{ $outOfStockCount }} en rupture</span>
        </div>

        <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($lowStockVariants as $variant)
                <a href="{{ route('admin.products.edit', $variant->product) }}" class="flex items-center justify-between py-3 text-sm transition hover:text-primary">
                    <span class="text-secondary-shade dark:text-white">{{ $variant->product->name }} <span class="text-xs text-grey dark:text-white/40">#{{ $variant->sku }}</span></span>
                    <span class="text-xs font-medium text-amber-600 dark:text-amber-400">{{ $variant->stock }} restant(s)</span>
                </a>
            @empty
                <p class="py-3 text-sm text-grey dark:text-white/40">Aucune alerte de stock faible.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Produits populaires --}}
<div class="mt-6 bg-white p-6 shadow-sm dark:bg-[#16201f]">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Produits populaires</h2>

    <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
        @forelse($popularProducts as $index => $product)
            <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center gap-4 py-3 text-sm transition hover:text-primary">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center bg-grey-tint text-[11px] font-bold text-grey dark:bg-white/10 dark:text-white/50">{{ $index + 1 }}</span>
                <div class="h-10 w-10 shrink-0 overflow-hidden bg-grey-tint dark:bg-white/10">
                    @if($image = $product->images->first()?->url)
                        <img src="{{ img_url($image, 80, 80) }}" alt="" class="h-full w-full object-cover">
                    @endif
                </div>
                <span class="flex-1 truncate text-secondary-shade dark:text-white">{{ $product->name }}</span>
                <span class="shrink-0 text-xs text-grey dark:text-white/40">{{ $product->total_sold ?? 0 }} vendu(s)</span>
            </a>
        @empty
            <p class="py-3 text-sm text-grey dark:text-white/40">Pas encore de ventes.</p>
        @endforelse
    </div>
</div>

@endsection
