@extends('layouts.app')

@section('title', 'Mes commandes — KhalilShop')

@section('content')
<div class="mx-auto max-w-3xl px-6 py-16 sm:px-10">

    <a href="{{ route('account.index') }}" class="text-xs text-grey hover:text-primary"><i class="fa-solid fa-arrow-left mr-1"></i>Mon compte</a>
    <h1 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">Mes commandes</h1>

    @php
        $statusLabels = [
            'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
            'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
        ];
    @endphp

    <div class="mt-10 divide-y divide-secondary-shade/10 border-t border-secondary-shade/10">
        @forelse($orders as $order)
            <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between py-5 text-sm hover:text-primary">
                <span>
                    <span class="font-medium text-secondary-shade">{{ $order->order_number }}</span>
                    <span class="ml-2 text-xs text-grey">{{ $order->created_at->format('d/m/Y') }}</span>
                    <span class="ml-2 text-xs uppercase tracking-[0.05em] text-grey">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                </span>
                <span class="text-xs font-medium text-secondary-shade">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <i class="fa-solid fa-bag-shopping mb-4 text-2xl text-secondary-shade/20"></i>
                <p class="text-sm text-grey">Vous n'avez pas encore passé de commande.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $orders->links() }}</div>

</div>
@endsection
