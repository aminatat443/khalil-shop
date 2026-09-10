@extends('layouts.admin')

@section('title', 'Campagnes email')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Campagnes email</h1>
    @can('create', App\Models\Campaign::class)
        <a href="{{ route('admin.campaigns.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Nouvelle campagne
        </a>
    @endcan
</div>

<div class="mt-8 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Sujet</th>
                <th class="px-6 py-4 font-medium">Type</th>
                <th class="px-6 py-4 font-medium">Produits</th>
                <th class="px-6 py-4 font-medium">Destinataires</th>
                <th class="px-6 py-4 font-medium">Envoyée par</th>
                <th class="px-6 py-4 font-medium">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($campaigns as $campaign)
                <tr>
                    <td class="px-6 py-4 font-medium text-secondary-shade">{{ $campaign->subject }}</td>
                    <td class="px-6 py-4 text-grey">{{ App\Models\Campaign::TYPES[$campaign->type] ?? $campaign->type }}</td>
                    <td class="px-6 py-4 text-grey">{{ count($campaign->product_ids) }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ $campaign->recipients_count }}</td>
                    <td class="px-6 py-4 text-grey">{{ $campaign->sender?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-xs text-grey">{{ $campaign->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-grey">Aucune campagne envoyée pour le moment.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $campaigns->links() }}</div>

@endsection
