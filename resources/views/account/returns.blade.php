@extends('layouts.app')

@section('title', 'Mes retours — KhalilShop')

@section('content')
<div class="mx-auto max-w-3xl px-6 py-16 sm:px-10">

    <a href="{{ route('account.index') }}" class="text-xs text-grey hover:text-primary"><i class="fa-solid fa-arrow-left mr-1"></i>Mon profil</a>
    <h1 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">Mes retours</h1>

    @php
        $statusLabels = [
            'demandee' => 'Demandé', 'acceptee' => 'Accepté', 'refusee' => 'Refusé',
            'article_recu' => 'Article reçu', 'remboursee' => 'Remboursé',
        ];
    @endphp

    <div class="mt-10 divide-y divide-secondary-shade/10 border-t border-secondary-shade/10">
        @forelse($returns as $return)
            <div class="py-5 text-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-secondary-shade">{{ $return->orderItem->product_name }}</p>
                        <p class="mt-1 text-xs text-grey">
                            Commande {{ $return->orderItem->order->order_number }} · demandé le {{ $return->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-[0.05em] {{ in_array($return->status, ['refusee']) ? 'text-primary' : 'text-secondary-shade' }}">
                        {{ $statusLabels[$return->status] ?? $return->status }}
                    </span>
                </div>
                @if($return->description)
                    <p class="mt-2 text-xs text-grey">{{ $return->description }}</p>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <i class="fa-solid fa-rotate-left mb-4 text-2xl text-secondary-shade/20"></i>
                <p class="text-sm text-grey">Aucune demande de retour pour le moment.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
