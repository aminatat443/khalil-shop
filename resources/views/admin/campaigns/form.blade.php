@extends('layouts.admin')

@section('title', 'Nouvelle campagne')

@section('content')

<a href="{{ route('admin.campaigns.index') }}" class="text-xs text-grey hover:text-primary dark:text-white/40"><i class="fa-solid fa-arrow-left mr-1"></i>Campagnes</a>
<h1 class="mt-3 font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Nouvelle campagne</h1>
<p class="mt-2 text-sm text-grey dark:text-white/40">L'email sera envoyé à tous les clients inscrits ayant vérifié leur adresse.</p>

<form
    action="{{ route('admin.campaigns.store') }}"
    method="POST"
    class="mt-8 max-w-3xl space-y-8"
    onsubmit="return confirm('Envoyer cette campagne à tous les clients inscrits ?');"
>
    @csrf

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Type de campagne</label>
                <select name="type" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
                    <option value="">Choisir…</option>
                    @foreach(App\Models\Campaign::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Sujet de l'email</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </div>
        </div>

        <div class="mt-6">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Message (optionnel)</label>
            <textarea name="message" rows="3" maxlength="2000" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">{{ old('message') }}</textarea>
        </div>
    </div>

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5" x-data="{ q: '' }">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
                <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-boxes-stacked text-[10px]"></i></span>
                Produits à inclure
            </h2>
            <input type="text" x-model="q" placeholder="Filtrer…" class="w-48 border border-secondary-shade/15 bg-white px-3.5 py-1.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            @foreach($products as $product)
                <label
                    x-show="q === '' || {{ Js::from(mb_strtolower($product->name)) }}.includes(q.toLowerCase())"
                    class="flex items-center gap-3 border border-secondary-shade/10 p-3 text-sm transition hover:border-primary dark:border-white/10"
                >
                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(collect(old('product_ids'))->contains($product->id)) class="h-4 w-4 border-gray-300 text-primary focus:ring-primary">
                    <span class="flex-1 truncate text-secondary-shade dark:text-white">{{ $product->name }}</span>
                    <span class="text-xs text-grey dark:text-white/40">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md">
        Envoyer la campagne
    </button>
</form>

@endsection
