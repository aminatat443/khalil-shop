@props(['order'])

@php
    $statusLabels = [
        'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
    ];

    // Transitions permises depuis chaque statut — mêmes règles que les boutons d'action de la
    // page détail commande (admin/orders/show.blade.php) : confirmer/annuler ont des effets sur
    // le stock et passent par leurs routes dédiées, les statuts logistiques par la route générique.
    $targets = match($order->status) {
        'recue' => ['confirmee', 'annulee'],
        'confirmee', 'en_preparation', 'expediee', 'en_livraison' => collect(['en_preparation', 'expediee', 'livree'])
            ->reject(fn ($s) => $s === $order->status)->push('annulee')->all(),
        default => [],
    };
@endphp

<div x-data="{ open: false }" class="relative inline-block" @click.stop @click.outside="open = false">
    <button
        type="button"
        @if(! empty($targets)) @click="open = ! open" @endif
        class="inline-flex items-center gap-1.5 text-xs {{ $order->status === 'annulee' ? 'text-red-600' : 'text-secondary-shade' }} {{ empty($targets) ? '' : 'cursor-pointer hover:text-primary' }}"
    >
        {{ $statusLabels[$order->status] ?? $order->status }}
        @unless(empty($targets))
            <i class="fa-solid fa-chevron-down text-[8px]"></i>
        @endunless
    </button>

    @unless(empty($targets))
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms @click.stop class="absolute left-0 top-full z-20 mt-1 w-44 border border-secondary-shade/10 bg-white py-1 text-left shadow-lg">
            @foreach($targets as $target)
                <form
                    action="{{ $target === 'confirmee' ? route('admin.orders.confirm', $order) : ($target === 'annulee' ? route('admin.orders.cancel', $order) : route('admin.orders.status', $order)) }}"
                    method="POST"
                    @if($target === 'confirmee') onsubmit="return confirm('Confirmer cette commande ? Le stock sera décrémenté.');"
                    @elseif($target === 'annulee') onsubmit="return confirm('Annuler cette commande ?');" @endif
                >
                    @csrf
                    @if($target !== 'confirmee' && $target !== 'annulee')
                        <input type="hidden" name="status" value="{{ $target }}">
                    @endif
                    <button type="submit" class="block w-full px-4 py-2 text-left text-xs text-secondary-shade transition hover:bg-grey-tint {{ $target === 'annulee' ? 'text-red-600' : '' }}">
                        {{ $statusLabels[$target] }}
                    </button>
                </form>
            @endforeach
        </div>
    @endunless
</div>
