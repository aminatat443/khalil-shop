@extends('layouts.app')

@section('title', 'Mes retours — KhalilShop')

@section('content')
<div class="mx-auto max-w-4xl px-6 py-16 sm:px-10">

    <a href="{{ route('account.index') }}" class="text-xs text-grey hover:text-primary"><i class="fa-solid fa-arrow-left mr-1"></i>Mon compte</a>
    <h1 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">Mes retours</h1>

    <div class="mt-8">
        <x-account-nav active="retours" />
    </div>

    <div class="mt-8 space-y-3">
        @forelse($returns as $return)
            <div class="border border-secondary-shade/10 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-medium text-secondary-shade">{{ $return->orderItem->product_name }}</p>
                        <p class="mt-1 text-xs text-grey">
                            Commande {{ $return->orderItem->order->order_number }} · demandé le {{ $return->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <x-status-pill :tone="\App\Models\ProductReturn::STATUS_TONES[$return->status] ?? 'neutral'" :label="\App\Models\ProductReturn::STATUS_LABELS[$return->status] ?? $return->status" />
                </div>
                @if($return->description)
                    <p class="mt-3 border-t border-secondary-shade/10 pt-3 text-xs text-grey">{{ $return->description }}</p>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center border border-dashed border-secondary-shade/15 py-20 text-center">
                <i class="fa-solid fa-rotate-left mb-4 text-2xl text-secondary-shade/20"></i>
                <p class="text-sm text-grey">Aucune demande de retour pour le moment.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
