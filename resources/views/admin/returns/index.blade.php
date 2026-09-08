@extends('layouts.admin')

@section('title', 'Retours')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Demandes de retour</h1>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher par n° de commande ou article…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-72 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Article</th>
                <th class="px-6 py-4 font-medium">Commande</th>
                <th class="px-6 py-4 font-medium">Motif</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($returns as $return)
                <tr>
                    <td class="px-6 py-4 text-secondary-shade">{{ $return->orderItem->product_name }}</td>
                    <td class="px-6 py-4 text-grey">{{ $return->orderItem->order->order_number }}</td>
                    <td class="px-6 py-4 text-grey">
                        {{ ['taille' => 'Taille', 'defaut' => 'Défaut', 'description' => 'Description', 'autre' => 'Autre'][$return->reason] ?? $return->reason }}
                        @if($return->description)
                            <span class="block text-xs text-grey/70">{{ $return->description }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.returns.update', $return) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="border-b border-secondary-shade/20 bg-transparent py-1 text-xs outline-none focus:border-primary">
                                @foreach(['demandee' => 'Demandé', 'acceptee' => 'Accepté', 'refusee' => 'Refusé', 'article_recu' => 'Article reçu', 'remboursee' => 'Remboursé'] as $value => $label)
                                    <option value="{{ $value }}" @selected($return->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right text-xs text-grey">{{ $return->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-grey">Aucune demande de retour.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $returns->links() }}</div>

@endsection
