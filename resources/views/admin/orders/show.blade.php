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

<div class="flex flex-wrap items-center justify-between gap-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-grey hover:text-primary"><i class="fa-solid fa-arrow-left mr-1"></i>Commandes</a>
        <h1 class="mt-2 font-display text-3xl font-normal italic text-secondary-shade">{{ $order->order_number }}</h1>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">
            <i class="fa-solid fa-file-invoice mr-1.5"></i>Facture
        </a>
        <span class="bg-grey-tint px-4 py-2 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade">{{ $statusLabels[$order->status] ?? $order->status }}</span>
    </div>
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">

    <div class="space-y-6">

        {{-- Articles --}}
        <div class="bg-white p-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Articles</h2>
            <div class="mt-4 divide-y divide-secondary-shade/10">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-3 text-sm">
                        <div>
                            <p class="font-medium text-secondary-shade">{{ $item->product_name }}</p>
                            @if($item->variant_label)
                                <p class="text-xs text-grey">{{ $item->variant_label }}</p>
                            @endif
                            <p class="text-xs text-grey">Qté : {{ $item->quantity }} · SKU {{ $item->sku ?? '—' }}</p>
                        </div>
                        <p class="font-medium text-secondary-shade">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-1.5 border-t border-secondary-shade/10 pt-4 text-sm text-grey">
                <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
                <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
                @if($order->discount > 0)
                    <div class="flex justify-between text-primary"><span>Réduction {{ $order->coupon?->code }}</span><span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span></div>
                @endif
            </div>
            <div class="mt-2 flex justify-between border-t border-secondary-shade/10 pt-2 text-sm font-semibold text-secondary-shade">
                <span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Client & livraison --}}
        <div class="bg-white p-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Client & livraison</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-grey">Nom</dt><dd class="text-secondary-shade">{{ $order->customer_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-grey">Téléphone</dt><dd class="text-secondary-shade">{{ $order->customer_phone }}</dd></div>
                <div class="flex justify-between"><dt class="text-grey">Email</dt><dd class="text-secondary-shade">{{ $order->customer_email }}</dd></div>
                <div class="flex justify-between"><dt class="text-grey">Adresse</dt><dd class="text-right text-secondary-shade">{{ $order->delivery_address }}, {{ $order->delivery_quartier }}<br>{{ $order->delivery_city }}, {{ $order->delivery_region }}</dd></div>
                @if($order->delivery_instructions)
                    <div class="flex justify-between"><dt class="text-grey">Instructions</dt><dd class="text-secondary-shade">{{ $order->delivery_instructions }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-grey">Paiement</dt><dd class="text-secondary-shade">{{ $order->payment_method === 'cod' ? 'À la livraison' : $order->payment_method }}</dd></div>
            </dl>
        </div>

    </div>

    {{-- Actions --}}
    <div class="space-y-4">
        <div class="bg-white p-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Actions</h2>

            @if($order->status === 'recue')
                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="mt-4" onsubmit="return confirm('Confirmer cette commande ? Le stock sera décrémenté.');">
                    @csrf
                    <button type="submit" class="w-full bg-secondary-shade py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                        Confirmer la commande
                    </button>
                </form>
            @endif

            @if(in_array($order->status, ['confirmee', 'en_preparation', 'expediee', 'en_livraison']))
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                    @csrf
                    <select name="status" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
                        @foreach(['en_preparation' => 'En préparation', 'expediee' => 'Expédiée', 'livree' => 'Livrée'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-3 w-full bg-secondary-shade py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                        Mettre à jour le statut
                    </button>
                </form>
            @endif

            @if(! in_array($order->status, ['annulee', 'livree']))
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="mt-4" onsubmit="return confirm('Annuler cette commande ?');">
                    @csrf
                    <button type="submit" class="w-full border border-primary py-3 text-xs font-semibold uppercase tracking-[0.15em] text-primary transition hover:bg-primary hover:text-white">
                        Annuler la commande
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>

@endsection
