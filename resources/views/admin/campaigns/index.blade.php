@extends('layouts.admin')

@section('title', 'Campagnes email')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">Campagnes email</h1>
    @can('create', App\Models\Campaign::class)
        <a href="{{ route('admin.campaigns.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white shadow-sm transition hover:bg-primary hover:shadow-md">
            Nouvelle campagne
        </a>
    @endcan
</div>

<div class="mt-8 overflow-x-auto bg-white shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey dark:border-white/10 dark:text-white/40">
                <th class="px-6 py-4 font-medium">Sujet</th>
                <th class="px-6 py-4 font-medium">Type</th>
                <th class="px-6 py-4 font-medium">Produits</th>
                <th class="px-6 py-4 font-medium">Destinataires</th>
                <th class="px-6 py-4 font-medium">Envoyée par</th>
                <th class="px-6 py-4 font-medium">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($campaigns as $campaign)
                <tr>
                    <td class="px-6 py-4 font-medium text-secondary-shade dark:text-white">{{ $campaign->subject }}</td>
                    <td class="px-6 py-4 text-grey dark:text-white/50">{{ App\Models\Campaign::TYPES[$campaign->type] ?? $campaign->type }}</td>
                    <td class="px-6 py-4 text-grey dark:text-white/50">{{ count($campaign->product_ids) }}</td>
                    <td class="px-6 py-4 text-secondary-shade dark:text-white">{{ $campaign->recipients_count }}</td>
                    <td class="px-6 py-4 text-grey dark:text-white/50">{{ $campaign->sender?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-xs text-grey dark:text-white/40">{{ $campaign->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-grey dark:text-white/40">Aucune campagne envoyée pour le moment.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $campaigns->links() }}</div>

@endsection
