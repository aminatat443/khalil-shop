@extends('layouts.admin-modal')

@php
    $modalBack = route('admin.orders.index');
@endphp

@section('title', $order->order_number)

@section('modal-width', 'max-w-4xl')

@section('modal')

@php
    $statusLabels = [
        'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
    ];
@endphp

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-grey hover:text-primary dark:text-white/40"><i class="fa-solid fa-arrow-left mr-1"></i>Commandes</a>
        <h1 class="mt-2 font-display text-2xl font-normal italic text-secondary-shade dark:text-white sm:text-3xl">
            {{ $order->order_number }}
            @if($order->is_in_store)
                <span class="ml-2 align-middle text-xs font-sans not-italic font-semibold uppercase tracking-[0.08em] text-grey dark:text-white/40"><i class="fa-solid fa-store mr-1"></i>Vente en boutique</span>
            @endif
        </h1>
    </div>
    <div class="flex items-center justify-between gap-4 sm:justify-end">
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="whitespace-nowrap text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary dark:text-white/70">
            <i class="fa-solid fa-file-invoice mr-1.5"></i>Facture
        </a>
        <span class="bg-grey-tint px-4 py-2 text-xs font-semibold uppercase tracking-[0.1em] dark:bg-white/10"><x-order-status-badge :order="$order" /></span>
    </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">

    <div class="space-y-6">

        {{-- Articles --}}
        <div class="bg-white p-6 dark:bg-white/5">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Articles</h2>
            <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between gap-3 py-3 text-sm">
                        <div class="min-w-0">
                            <p class="break-words font-medium text-secondary-shade dark:text-white">{{ $item->product_name }}</p>
                            @if($item->variant_label)
                                <p class="text-xs text-grey dark:text-white/40">{{ $item->variant_label }}</p>
                            @endif
                            <p class="text-xs text-grey dark:text-white/40">Qté : {{ $item->quantity }} · SKU {{ $item->sku ?? '—' }}</p>
                        </div>
                        <p class="shrink-0 whitespace-nowrap font-medium text-secondary-shade dark:text-white">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-1.5 border-t border-secondary-shade/10 pt-4 text-sm text-grey dark:border-white/10 dark:text-white/50">
                <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                @if($order->discount > 0)
                    <div class="flex justify-between text-primary"><span>Réduction {{ $order->coupon?->code }}</span><span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span></div>
                @endif
            </div>
            <div class="mt-2 flex justify-between border-t border-secondary-shade/10 pt-2 text-sm font-semibold text-secondary-shade dark:border-white/10 dark:text-white">
                <span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Client & livraison --}}
        <div class="bg-white p-6 dark:bg-white/5">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Client & livraison</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-grey dark:text-white/40">Nom</dt><dd class="text-secondary-shade dark:text-white">{{ $order->customer_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-grey dark:text-white/40">Téléphone</dt><dd class="text-secondary-shade dark:text-white">{{ $order->customer_phone }}</dd></div>
                <div class="flex justify-between"><dt class="text-grey dark:text-white/40">Email</dt><dd class="text-secondary-shade dark:text-white">{{ $order->customer_email }}</dd></div>
                <div class="flex justify-between">
                    <dt class="text-grey dark:text-white/40">Adresse</dt>
                    <dd class="text-right text-secondary-shade dark:text-white">
                        @if($order->is_in_store)
                            Retrait en boutique
                        @else
                            {{ $order->delivery_address }}, {{ $order->delivery_quartier }}<br>{{ $order->delivery_city }}, {{ $order->delivery_region }}
                        @endif
                    </dd>
                </div>
                @if($order->delivery_instructions)
                    <div class="flex justify-between"><dt class="text-grey dark:text-white/40">Instructions</dt><dd class="text-secondary-shade dark:text-white">{{ $order->delivery_instructions }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-grey dark:text-white/40">Paiement</dt><dd class="text-secondary-shade dark:text-white">{{ $order->paymentMethodLabel() }}</dd></div>
            </dl>
        </div>

    </div>

    {{-- Actions --}}
    <div class="space-y-4">
        <div class="bg-white p-6 dark:bg-white/5">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Actions</h2>

            @if($order->status === 'recue')
                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="mt-4" onsubmit="return confirm('Confirmer cette commande ? Le stock sera décrémenté.');">
                    @csrf
                    <button type="submit" class="w-full bg-secondary-shade py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                        Confirmer la commande
                    </button>
                </form>
            @endif

            @if(! in_array($order->status, ['annulee', 'livree']))
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-4" onsubmit="return confirm('Annuler cette commande ?');">
                    @csrf
                    <button type="submit" class="w-full border border-red-300 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-red-600 transition hover:bg-red-600 hover:text-white dark:border-red-500/30 dark:text-red-400">
                        Annuler la commande
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

@endsection
