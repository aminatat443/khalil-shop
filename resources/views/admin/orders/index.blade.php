@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')

<div x-data="ajaxFilter()">

<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Commandes</h1>
    @can('create', App\Models\Order::class)
        <a href="{{ route('admin.orders.create') }}" class="flex items-center gap-2 bg-secondary-shade px-5 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-primary">
            <i class="fa-solid fa-store"></i>
            Vente en boutique
        </a>
    @endcan
</div>

@php
    $statusLabels = [
        'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
    ];
@endphp

<form method="GET" @submit.prevent="submitForm($event)" class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="N° de commande ou client…" @input.debounce.500ms="$el.form.requestSubmit()" @if(request()->filled('q')) autofocus @endif class="w-64 border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    <select name="status" @change="$el.form.requestSubmit()" class="border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        <option value="">Tous les statuts</option>
        @foreach($statusLabels as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <noscript><button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary dark:text-white/70">Filtrer</button></noscript>
    <i x-show="loading" x-cloak class="fa-solid fa-circle-notch fa-spin text-secondary-shade/40 dark:text-white/30"></i>
</form>

<div x-ref="results" @click="onResultsClick($event)" :class="loading && 'opacity-50 pointer-events-none'" class="mt-6 transition-opacity">
    @include('admin.orders.partials.table')
</div>

</div>

@endsection
