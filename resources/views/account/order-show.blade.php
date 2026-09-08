@extends('layouts.app')

@section('title', $order->order_number.' — KhalilShop')

@section('content')

@php
    $steps = [
        'recue' => 'Reçue',
        'confirmee' => 'Confirmée',
        'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée',
        'livree' => 'Livrée',
    ];
    $currentIndex = array_search($order->status, array_keys($steps));
@endphp

<div class="mx-auto max-w-3xl px-6 py-16 sm:px-10">

    <a href="{{ route('account.orders') }}" class="text-xs text-grey hover:text-primary"><i class="fa-solid fa-arrow-left mr-1"></i>Mes commandes</a>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-4xl font-normal italic text-secondary-shade">{{ $order->order_number }}</h1>
            <p class="mt-1 text-xs text-grey">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:text-primary">
            <i class="fa-solid fa-file-invoice mr-1.5"></i>Télécharger la facture
        </a>
    </div>

    {{-- Suivi de commande (section 38 du cahier des charges) --}}
    @if($order->status === 'annulee')
        <div class="mt-10 border border-primary/30 bg-primary-tint px-6 py-5 text-sm text-primary-shade">
            <i class="fa-solid fa-circle-xmark mr-2"></i>Cette commande a été annulée.
        </div>
    @else
        <div class="mt-12 flex items-start justify-between">
            @foreach($steps as $value => $label)
                @php($stepIndex = array_search($value, array_keys($steps)))
                <div class="flex flex-1 flex-col items-center text-center">
                    <div class="flex w-full items-center">
                        <div class="h-px flex-1 {{ $stepIndex === 0 ? 'bg-transparent' : ($stepIndex <= $currentIndex ? 'bg-secondary-shade' : 'bg-grey-tint') }}"></div>
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold {{ $stepIndex <= $currentIndex ? 'bg-secondary-shade text-white' : 'bg-grey-tint text-grey' }}">
                            @if($stepIndex < $currentIndex)
                                <i class="fa-solid fa-check text-[9px]"></i>
                            @else
                                {{ $stepIndex + 1 }}
                            @endif
                        </span>
                        <div class="h-px flex-1 {{ $stepIndex === count($steps) - 1 ? 'bg-transparent' : ($stepIndex < $currentIndex ? 'bg-secondary-shade' : 'bg-grey-tint') }}"></div>
                    </div>
                    <p class="mt-2 text-[10px] uppercase tracking-[0.05em] {{ $stepIndex <= $currentIndex ? 'text-secondary-shade' : 'text-grey' }}">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Articles --}}
    <div class="mt-14 divide-y divide-secondary-shade/10 border-y border-secondary-shade/10">
        @foreach($order->items as $item)
            <div class="py-4 text-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-secondary-shade">{{ $item->product_name }}</p>
                        @if($item->variant_label)
                            <p class="text-xs text-grey">{{ $item->variant_label }}</p>
                        @endif
                        <p class="text-xs text-grey">Qté : {{ $item->quantity }}</p>
                    </div>
                    <p class="font-medium text-secondary-shade">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</p>
                </div>

                {{-- Demande de retour (docs/SPEC.md §2.1 — 7 jours après livraison) --}}
                @if($order->status === 'livree')
                    @if($existingReturn = $item->returns->first())
                        <p class="mt-3 text-xs text-grey">
                            <i class="fa-solid fa-rotate-left mr-1.5"></i>
                            Retour {{ ['demandee' => 'demandé', 'acceptee' => 'accepté', 'refusee' => 'refusé', 'article_recu' => 'article reçu', 'remboursee' => 'remboursé'][$existingReturn->status] ?? $existingReturn->status }}
                        </p>
                    @elseif($order->updated_at->diffInDays(now()) <= 7)
                        <div x-data="{ open: false, reason: '' }" class="mt-3">
                            <button type="button" @click="open = !open" class="text-xs font-semibold text-primary hover:underline">
                                Demander un retour
                            </button>

                            <form x-show="open" x-cloak action="{{ route('account.returns.store') }}" method="POST" class="mt-3 space-y-3 border border-secondary-shade/10 p-4">
                                @csrf
                                <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                <select name="reason" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
                                    <option value="">Motif du retour</option>
                                    <option value="taille">Taille inadaptée</option>
                                    <option value="defaut">Article défectueux</option>
                                    <option value="description">Ne correspond pas à la description</option>
                                    <option value="autre">Autre</option>
                                </select>
                                <textarea name="description" rows="2" placeholder="Précisez (optionnel)" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary"></textarea>
                                <button type="submit" class="bg-secondary-shade px-5 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-primary">
                                    Envoyer la demande
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-4 space-y-1.5 text-sm text-grey">
        <div class="flex justify-between"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
        <div class="flex justify-between"><span>Livraison</span><span>{{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</span></div>
        @if($order->discount > 0)
            <div class="flex justify-between text-primary"><span>Réduction</span><span>-{{ number_format($order->discount, 0, ',', ' ') }} FCFA</span></div>
        @endif
    </div>
    <div class="mt-3 flex justify-between border-t border-secondary-shade/10 pt-3">
        <span class="text-sm font-medium uppercase tracking-[0.1em] text-secondary-shade">Total</span>
        <span class="font-display text-xl italic text-secondary-shade">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
    </div>

    <div class="mt-8 border border-secondary-shade/10 p-6 text-sm text-grey">
        <p><span class="font-medium text-secondary-shade">Livraison à :</span> {{ $order->delivery_address }}, {{ $order->delivery_quartier }} {{ $order->delivery_city }}, {{ $order->delivery_region }}</p>
        <p class="mt-2"><span class="font-medium text-secondary-shade">Paiement :</span> {{ $order->payment_method === 'cod' ? 'À la livraison' : $order->payment_method }}</p>
    </div>

</div>
@endsection
