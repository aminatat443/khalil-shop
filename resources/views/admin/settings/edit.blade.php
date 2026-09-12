@extends('layouts.admin')

@section('title', 'Configuration')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Configuration</h1>
<p class="mt-2 text-sm text-grey dark:text-white/40">Coordonnées de la boutique et visuels affichés sur la facture PDF envoyée aux clients.</p>
<p class="mt-3 inline-block bg-grey-tint px-3 py-1.5 text-xs text-grey dark:bg-white/10 dark:text-white/40">
    Stockage des images : <strong class="text-secondary-shade dark:text-white">{{ App\Models\Setting::mediaDisk() === 'cloudinary' ? 'Cloudinary' : 'Local' }}</strong>
    @if(App\Models\Setting::mediaDisk() !== 'cloudinary')
        — ajoutez <code>CLOUDINARY_URL</code> dans le fichier .env pour basculer automatiquement sur Cloudinary.
    @endif
</p>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 max-w-2xl space-y-10">
    @csrf

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
            <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-address-card text-[10px]"></i></span>
            Coordonnées (émetteur de la facture)
        </h2>

        <div class="mt-5 grid gap-6 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Nom de la boutique</label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $settings->shop_name) }}" required class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm text-secondary-shade outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Téléphone</label>
                <input type="text" name="shop_phone" value="{{ old('shop_phone', $settings->shop_phone) }}" placeholder="+221 77 000 00 00" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm text-secondary-shade outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Adresse</label>
                <input type="text" name="shop_address" value="{{ old('shop_address', $settings->shop_address) }}" placeholder="Dakar, Sénégal" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm text-secondary-shade outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Email</label>
                <input type="email" name="shop_email" value="{{ old('shop_email', $settings->shop_email) }}" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm text-secondary-shade outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </div>
        </div>
    </div>

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
            <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-image text-[10px]"></i></span>
            Logo de la facture
        </h2>
        <p class="mt-1 text-xs text-grey dark:text-white/40">Affiché en haut à gauche de chaque facture PDF.</p>

        <div class="mt-4 flex items-center gap-6">
            @if($logoUrl = $settings->mediaUrl('invoice_logo'))
                <img src="{{ $logoUrl }}" alt="Logo" class="h-16 w-auto border border-secondary-shade/10 bg-grey-tint p-2 dark:border-white/10 dark:bg-white/5">
            @endif
            <input type="file" name="invoice_logo" accept="image/*" class="text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em] dark:text-white dark:file:bg-white/10">
        </div>
    </div>

    <div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
        <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
            <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-signature text-[10px]"></i></span>
            Signature &amp; cachet
        </h2>
        <p class="mt-1 text-xs text-grey dark:text-white/40">Photo de la signature et du cachet, affichée en bas de la facture PDF.</p>

        <div class="mt-4 flex items-center gap-6">
            @if($signatureUrl = $settings->mediaUrl('invoice_signature'))
                <img src="{{ $signatureUrl }}" alt="Signature et cachet" class="h-20 w-auto border border-secondary-shade/10 bg-grey-tint p-2 dark:border-white/10 dark:bg-white/5">
            @endif
            <input type="file" name="invoice_signature" accept="image/*" class="text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em] dark:text-white dark:file:bg-white/10">
        </div>
    </div>

    <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md">
        Enregistrer
    </button>
</form>

@endsection
