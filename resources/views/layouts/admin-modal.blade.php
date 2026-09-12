{{-- Habillage "modale flottante" pour les pages de création/modification du back-office --}}
@extends('layouts.admin')

@section('content')

<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 10)"
    @keydown.escape.window="window.location = '{{ $modalBack ?? route('admin.dashboard') }}'"
    class="fixed inset-0 z-50"
>

    <a
        href="{{ $modalBack ?? route('admin.dashboard') }}"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-secondary-shade/50 backdrop-blur-sm"
        aria-label="Fermer"
    ></a>

    <div class="relative flex h-full items-start justify-center overflow-y-auto px-4 py-10 sm:py-16">
        <div
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full @yield('modal-width', 'max-w-xl') overflow-hidden bg-white shadow-2xl ring-1 ring-black/5 dark:bg-[#16201f] dark:ring-white/10"
        >

            <div class="h-1.5 bg-gradient-to-r from-secondary-shade via-primary to-secondary-shade"></div>

            <a href="{{ $modalBack ?? route('admin.dashboard') }}" class="absolute right-5 top-7 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-grey-tint/70 text-secondary-shade/60 transition hover:bg-grey-tint hover:text-primary dark:bg-white/5 dark:text-white/50 dark:hover:bg-white/10 dark:hover:text-primary" aria-label="Fermer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </a>

            <div class="max-h-[calc(85vh-0.375rem)] overflow-y-auto p-5 sm:p-10">
                @yield('modal')
            </div>

        </div>
    </div>

</div>

@endsection
