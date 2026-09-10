@extends('layouts.admin')

@section('title', 'Codes promo')

@section('content')

<div class="flex items-center justify-between">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Codes promo</h1>
    @can('create', App\Models\Coupon::class)
        <a href="{{ route('admin.coupons.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Nouveau code
        </a>
    @endcan
</div>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un code…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-64 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Code</th>
                <th class="px-6 py-4 font-medium">Réduction</th>
                <th class="px-6 py-4 font-medium">Minimum</th>
                <th class="px-6 py-4 font-medium">Utilisations</th>
                <th class="px-6 py-4 font-medium">Expiration</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($coupons as $coupon)
                <tr onclick="window.location='{{ route('admin.coupons.edit', $coupon) }}'" class="cursor-pointer transition hover:bg-grey-tint/40">
                    <td class="px-6 py-4 font-medium text-secondary-shade">{{ $coupon->code }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format($coupon->value, 0, ',', ' ').' FCFA' }}</td>
                    <td class="px-6 py-4 text-grey">{{ $coupon->min_amount ? number_format($coupon->min_amount, 0, ',', ' ').' FCFA' : '—' }}</td>
                    <td class="px-6 py-4 text-grey">{{ $coupon->usages()->count() }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                    <td class="px-6 py-4 text-xs text-grey">{{ $coupon->expires_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $coupon->is_active ? 'text-primary' : 'text-grey' }}">{{ $coupon->is_active ? 'Actif' : 'Désactivé' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs text-secondary-shade hover:text-primary">Modifier</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-grey">Aucun code promo pour le moment.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $coupons->links() }}</div>

@endsection
