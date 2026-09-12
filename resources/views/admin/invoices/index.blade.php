@extends('layouts.admin')

@section('title', 'Factures')

@section('content')

<div x-data="ajaxFilter()">

<h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Factures</h1>
<p class="mt-1 text-sm text-grey dark:text-white/40">Registre des factures — une par commande, identique au PDF envoyé au client.</p>

<form method="GET" @submit.prevent="submitForm($event)" class="mt-8 flex flex-wrap items-end gap-4">
    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Recherche</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="N° de commande ou client…" @input.debounce.500ms="$el.form.requestSubmit()" class="w-64 border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Du</label>
        <input type="date" name="from" value="{{ request('from') }}" @change="$el.form.requestSubmit()" class="border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Au</label>
        <input type="date" name="to" value="{{ request('to') }}" @change="$el.form.requestSubmit()" class="border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </div>
    <noscript><button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary dark:text-white/70">Filtrer</button></noscript>
    <i x-show="loading" x-cloak class="fa-solid fa-circle-notch fa-spin text-secondary-shade/40 dark:text-white/30"></i>
</form>

<div x-ref="results" @click="onResultsClick($event)" :class="loading && 'opacity-50 pointer-events-none'" class="mt-6 transition-opacity">
    @include('admin.invoices.partials.table')
</div>

</div>

@endsection
