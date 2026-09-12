@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')

<div class="relative overflow-hidden bg-gradient-to-br from-secondary-shade via-[#233f3d] to-[#1a2f2d] px-7 py-8 shadow-lg sm:px-10 sm:py-10">
    <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-primary/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-16 left-1/3 h-48 w-48 rounded-full bg-white/[0.04] blur-3xl"></div>

    <div class="relative flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-white/40">{{ now()->translatedFormat('l d F Y') }}</p>
            <h1 class="mt-2 font-display text-3xl font-normal italic text-white sm:text-4xl">Bon retour, {{ explode(' ', auth()->user()->name)[0] }}.</h1>
            <p class="mt-2 max-w-md text-sm text-white/50">Voici un aperçu de l'activité de KhalilShop aujourd'hui.</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-white/10 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white ring-1 ring-white/10 transition hover:bg-white/15">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>Voir la boutique
        </a>
    </div>
</div>

{{-- Indicateurs clés (section 40) --}}
<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
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

    @foreach($kpis as $i => $kpi)
        <div class="group animate-fade-up bg-white p-4 shadow-sm ring-1 ring-secondary-shade/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-[#16201f] dark:ring-white/5 sm:p-6" style="animation-delay: {{ $i * 60 }}ms">
            <div class="flex items-center justify-between gap-2">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center {{ $toneClasses[$kpi['tone']] }} sm:h-11 sm:w-11">
                    <i class="fa-solid {{ $kpi['icon'] }} text-sm"></i>
                </span>
                @isset($kpi['trend'])
                    <span class="inline-flex shrink-0 items-center gap-1 text-[11px] font-semibold {{ $kpi['trend'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        <i class="fa-solid {{ $kpi['trend'] >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($kpi['trend']) }}%
                    </span>
                @endisset
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">{{ $kpi['label'] }}</p>
            <p class="mt-1 whitespace-nowrap font-display text-lg italic text-secondary-shade dark:text-white sm:text-2xl">{{ $kpi['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1.3fr_1fr]">

    {{-- Chiffre d'affaires — 7 derniers jours --}}
    <div class="animate-fade-up bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5" style="animation-delay: 240ms">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Chiffre d'affaires — 7 derniers jours</h2>

        @php($maxRevenue = max($last7Days->max('revenue'), 1))
        <div class="mt-6 flex h-36 items-end gap-3">
            @foreach($last7Days as $day)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div class="flex h-28 w-full items-end">
                        <div
                            class="w-full rounded-t-lg bg-gradient-to-t from-primary/25 to-primary/50 transition hover:from-primary/40 hover:to-primary dark:from-primary/25 dark:to-primary/60"
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
    <div class="grid grid-cols-2 gap-4 animate-fade-up" style="animation-delay: 300ms">
        <a href="{{ route('admin.returns.index') }}" class="flex flex-col justify-between bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-[#16201f] dark:ring-white/5">
            <span class="flex h-11 w-11 items-center justify-center bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                <i class="fa-solid fa-rotate-left text-sm"></i>
            </span>
            <div class="mt-4">
                <p class="font-display text-2xl italic text-secondary-shade dark:text-white">{{ $pendingReturnsCount }}</p>
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-grey dark:text-white/40">Retour(s) en attente</p>
            </div>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex flex-col justify-between bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-[#16201f] dark:ring-white/5">
            <span class="flex h-11 w-11 items-center justify-center bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400">
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
    <div class="animate-fade-up bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5" style="animation-delay: 360ms">
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
    <div class="animate-fade-up bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5" style="animation-delay: 420ms">
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
<div class="mt-6 animate-fade-up bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5" style="animation-delay: 480ms">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Produits populaires</h2>

    <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
        @forelse($popularProducts as $index => $product)
            <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center gap-4 py-3 text-sm transition hover:text-primary">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-grey-tint text-[11px] font-bold text-grey dark:bg-white/10 dark:text-white/50">{{ $index + 1 }}</span>
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
