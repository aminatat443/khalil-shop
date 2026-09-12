@extends('layouts.admin')

@section('title', 'Finances')

@section('content')

@php
    $paymentLabels = \App\Models\Order::PAYMENT_METHOD_LABELS;
@endphp

<div>
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Finances</h1>
    <p class="mt-1 text-sm text-grey dark:text-white/40">Chiffre d'affaires des commandes confirmées et suivantes.</p>
</div>

<div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <span class="flex h-11 w-11 items-center justify-center bg-primary-tint text-primary-shade dark:bg-primary/15 dark:text-primary">
            <i class="fa-solid fa-sack-dollar text-sm"></i>
        </span>
        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">Chiffre d'affaires total</p>
        <p class="mt-1 font-display text-2xl italic text-secondary-shade dark:text-white">{{ number_format($revenue, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <div class="flex items-center justify-between">
            <span class="flex h-11 w-11 items-center justify-center bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                <i class="fa-solid fa-calendar-days text-sm"></i>
            </span>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold {{ $monthTrend >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                <i class="fa-solid {{ $monthTrend >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                {{ abs($monthTrend) }}%
            </span>
        </div>
        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">Ce mois-ci</p>
        <p class="mt-1 font-display text-2xl italic text-secondary-shade dark:text-white">{{ number_format($thisMonth, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <span class="flex h-11 w-11 items-center justify-center bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400">
            <i class="fa-solid fa-basket-shopping text-sm"></i>
        </span>
        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">Panier moyen</p>
        <p class="mt-1 font-display text-2xl italic text-secondary-shade dark:text-white">{{ number_format($averageOrder, 0, ',', ' ') }} FCFA</p>
    </div>
    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <span class="flex h-11 w-11 items-center justify-center bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
            <i class="fa-solid fa-tag text-sm"></i>
        </span>
        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.15em] text-grey dark:text-white/40">Remises accordées</p>
        <p class="mt-1 font-display text-2xl italic text-secondary-shade dark:text-white">{{ number_format($totalDiscounts, 0, ',', ' ') }} FCFA</p>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1.3fr_1fr]">

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Chiffre d'affaires — 6 derniers mois</h2>

        @php($maxRevenue = max($last6Months->max('revenue'), 1))
        <div class="mt-6 flex h-36 items-end gap-3">
            @foreach($last6Months as $month)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <div class="flex h-28 w-full items-end">
                        <div
                            class="w-full bg-gradient-to-t from-primary/25 to-primary/50 transition hover:from-primary/40 hover:to-primary dark:from-primary/25 dark:to-primary/60"
                            style="height: {{ $month['revenue'] > 0 ? max(6, round(($month['revenue'] / $maxRevenue) * 100)) : 2 }}%"
                            title="{{ number_format($month['revenue'], 0, ',', ' ') }} FCFA"
                        ></div>
                    </div>
                    <span class="text-[10px] uppercase text-grey dark:text-white/40">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Répartition par moyen de paiement</h2>

        <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($byPaymentMethod as $row)
                <div class="flex items-center justify-between py-3 text-sm">
                    <span class="text-secondary-shade dark:text-white">{{ $paymentLabels[$row->payment_method] ?? $row->payment_method }}</span>
                    <span class="text-right">
                        <span class="block font-medium text-secondary-shade dark:text-white">{{ number_format($row->total, 0, ',', ' ') }} FCFA</span>
                        <span class="block text-xs text-grey dark:text-white/40">{{ $row->orders_count }} commande(s)</span>
                    </span>
                </div>
            @empty
                <p class="py-3 text-sm text-grey dark:text-white/40">Aucune donnée pour le moment.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
