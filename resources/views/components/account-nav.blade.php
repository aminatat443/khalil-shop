@props(['active'])

@php
    $links = [
        ['key' => 'profil', 'route' => 'account.index', 'icon' => 'fa-user', 'label' => 'Profil'],
        ['key' => 'commandes', 'route' => 'account.orders', 'icon' => 'fa-bag-shopping', 'label' => 'Commandes'],
        ['key' => 'retours', 'route' => 'account.returns', 'icon' => 'fa-rotate-left', 'label' => 'Retours'],
    ];
@endphp

<div class="flex gap-1 overflow-x-auto border-b border-secondary-shade/10 pb-px">
    @foreach($links as $link)
        <a
            href="{{ route($link['route']) }}"
            class="flex items-center gap-2 whitespace-nowrap border-b-2 px-4 py-3 text-xs font-semibold uppercase tracking-[0.1em] transition {{ $active === $link['key'] ? 'border-primary text-secondary-shade' : 'border-transparent text-grey hover:text-secondary-shade' }}"
        >
            <i class="fa-solid {{ $link['icon'] }} text-[11px]"></i>
            {{ $link['label'] }}
        </a>
    @endforeach
</div>
