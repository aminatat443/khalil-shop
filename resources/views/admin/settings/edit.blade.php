@extends('layouts.admin')

@section('title', 'Configuration')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Configuration</h1>
<p class="mt-2 text-sm text-grey">Coordonnées de la boutique et visuels affichés sur la facture PDF envoyée aux clients.</p>
<p class="mt-3 inline-block bg-grey-tint px-3 py-1.5 text-xs text-grey">
    Stockage des images : <strong class="text-secondary-shade">{{ App\Models\Setting::mediaDisk() === 'cloudinary' ? 'Cloudinary' : 'Local' }}</strong>
    @if(App\Models\Setting::mediaDisk() !== 'cloudinary')
        — ajoutez <code>CLOUDINARY_URL</code> dans le fichier .env pour basculer automatiquement sur Cloudinary.
    @endif
</p>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 max-w-2xl space-y-10">
    @csrf

    <div class="bg-white p-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Coordonnées (émetteur de la facture)</h2>

        <div class="mt-5 grid gap-6 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Nom de la boutique</label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $settings->shop_name) }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Téléphone</label>
                <input type="text" name="shop_phone" value="{{ old('shop_phone', $settings->shop_phone) }}" placeholder="+221 77 000 00 00" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Adresse</label>
                <input type="text" name="shop_address" value="{{ old('shop_address', $settings->shop_address) }}" placeholder="Dakar, Sénégal" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Email</label>
                <input type="email" name="shop_email" value="{{ old('shop_email', $settings->shop_email) }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>
        </div>
    </div>

    <div class="bg-white p-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Logo de la facture</h2>
        <p class="mt-1 text-xs text-grey">Affiché en haut à gauche de chaque facture PDF.</p>

        <div class="mt-4 flex items-center gap-6">
            @if($settings->invoice_logo)
                <img src="{{ $settings->mediaUrl('invoice_logo') }}" alt="Logo" class="h-16 w-auto border border-secondary-shade/10 bg-grey-tint p-2">
            @endif
            <input type="file" name="invoice_logo" accept="image/*" class="text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em]">
        </div>
    </div>

    <div class="bg-white p-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Signature &amp; cachet</h2>
        <p class="mt-1 text-xs text-grey">Photo de la signature et du cachet, affichée en bas de la facture PDF.</p>

        <div class="mt-4 flex items-center gap-6">
            @if($settings->invoice_signature)
                <img src="{{ $settings->mediaUrl('invoice_signature') }}" alt="Signature et cachet" class="h-20 w-auto border border-secondary-shade/10 bg-grey-tint p-2">
            @endif
            <input type="file" name="invoice_signature" accept="image/*" class="text-sm text-secondary-shade file:mr-4 file:border-0 file:bg-grey-tint file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-[0.1em]">
        </div>
    </div>

    <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
        Enregistrer
    </button>
</form>

@endsection
