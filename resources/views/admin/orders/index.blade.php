@extends('layouts.admin')

@section('title', 'Commandes')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Commandes</h1>

@php
    $statusLabels = [
        'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
    ];
@endphp

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="N° de commande ou client…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-64 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <select name="status" onchange="this.form.submit()" class="border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        <option value="">Tous les statuts</option>
        @foreach($statusLabels as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">N°</th>
                <th class="px-6 py-4 font-medium">Client</th>
                <th class="px-6 py-4 font-medium">Total</th>
                <th class="px-6 py-4 font-medium">Paiement</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium">Date</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($orders as $order)
                <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'" class="cursor-pointer transition hover:bg-grey-tint/40">
                    <td class="px-6 py-4 font-medium text-secondary-shade">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 text-grey">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-grey">{{ $order->payment_status === 'paid' ? 'Payé' : 'En attente' }}</td>
                    <td class="px-6 py-4" onclick="event.stopPropagation()">
                        <x-order-status-badge :order="$order" />
                    </td>
                    <td class="px-6 py-4 text-xs text-grey">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                        <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="mr-4 text-xs text-secondary-shade hover:text-primary" aria-label="Facture">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-secondary-shade hover:text-primary">Voir</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-grey">Aucune commande ne correspond à ces critères.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $orders->links() }}</div>

@endsection
