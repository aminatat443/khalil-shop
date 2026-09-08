@extends('layouts.app')

@section('title', 'Commande confirmée — KhalilShop')

@section('content')
<div class="mx-auto max-w-2xl px-6 py-20 sm:px-10">

    <div class="text-center">
        <i class="fa-solid fa-circle-check text-3xl text-primary"></i>
        <h1 class="mt-6 font-display text-4xl font-normal italic text-secondary-shade">Merci {{ $order->customer_name }} !</h1>
        <p class="mt-3 text-sm text-grey">
            Votre commande <span class="font-medium text-secondary-shade">{{ $order->order_number }}</span> a bien été reçue.
        </p>
    </div>

    <div class="mt-12 border border-secondary-shade/10 p-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-grey">Récapitulatif</h2>

        <div class="mt-4 divide-y divide-secondary-shade/10">
            @foreach($order->items as $item)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-secondary-shade">{{ $item->product_name }}</p>
                        @if($item->variant_label)
                            <p class="text-xs text-grey">{{ $item->variant_label }}</p>
                        @endif
                        <p class="text-xs text-grey">Qté : {{ $item->quantity }}</p>
                    </div>
                    <p class="text-sm font-medium text-secondary-shade">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 space-y-1.5 border-t border-secondary-shade/10 pt-4 text-sm text-grey">
            <div class="flex justify-between">
                <span>Sous-total</span>
                <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="flex justify-between">
                <span>Livraison</span>
                <span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-primary">
                    <span>Réduction</span>
                    <span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span>
                </div>
            @endif
        </div>

        <div class="mt-3 flex justify-between border-t border-secondary-shade/10 pt-3">
            <span class="text-sm font-medium uppercase tracking-[0.1em] text-secondary-shade">Total</span>
            <span class="font-display text-xl italic text-secondary-shade">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="mt-6 border border-secondary-shade/10 p-6 text-sm text-grey">
        <p><span class="font-medium text-secondary-shade">Livraison à :</span> {{ $order->delivery_address }}, {{ $order->delivery_quartier }} {{ $order->delivery_city }}, {{ $order->delivery_region }}</p>
        <p class="mt-2"><span class="font-medium text-secondary-shade">Paiement :</span> Paiement à la livraison</p>
        <p class="mt-2"><span class="font-medium text-secondary-shade">Statut :</span> Commande reçue, confirmation à venir</p>
    </div>

    <div class="mt-10 flex flex-wrap items-center justify-center gap-6">
        <a href="{{ route('home') }}" class="inline-block bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Retour à la boutique
        </a>
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">
            <i class="fa-solid fa-file-invoice mr-1.5"></i>Télécharger la facture
        </a>
    </div>

</div>
@endsection
