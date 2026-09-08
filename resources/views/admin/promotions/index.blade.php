@extends('layouts.admin')

@section('title', 'Promotions')

@section('content')

<div class="flex items-center justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Promotions</h1>
    @can('create', App\Models\Promotion::class)
        <a href="{{ route('admin.promotions.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Nouvelle promotion
        </a>
    @endcan
</div>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher par produit ou catégorie…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-64 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Cible</th>
                <th class="px-6 py-4 font-medium">Réduction</th>
                <th class="px-6 py-4 font-medium">Période</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($promotions as $promotion)
                <tr>
                    <td class="px-6 py-4 text-secondary-shade">{{ $promotion->product?->name ?? $promotion->category?->name ?? 'Toutes catégories' }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ $promotion->type === 'percentage' ? $promotion->value.'%' : number_format($promotion->value, 0, ',', ' ').' FCFA' }}</td>
                    <td class="px-6 py-4 text-xs text-grey">
                        {{ $promotion->starts_at?->format('d/m/Y') ?? '—' }} → {{ $promotion->ends_at?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $promotion->is_active ? 'text-primary' : 'text-grey' }}">{{ $promotion->is_active ? 'Active' : 'Désactivée' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-xs text-secondary-shade hover:text-primary">Modifier</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-grey">Aucune promotion pour le moment.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $promotions->links() }}</div>

@endsection
