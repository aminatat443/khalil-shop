{{-- Habillage "modale flottante" pour les pages de création/modification du back-office --}}
@extends('layouts.admin')

@section('content')

<div x-data @keydown.escape.window="window.location = '{{ $modalBack ?? route('admin.dashboard') }}'" class="fixed inset-0 z-50">

    <a href="{{ $modalBack ?? route('admin.dashboard') }}" class="absolute inset-0 bg-secondary-shade/50 backdrop-blur-[2px]" aria-label="Fermer"></a>

    <div class="relative flex h-full items-start justify-center overflow-y-auto px-4 py-10 sm:py-16">
        <div class="relative w-full @yield('modal-width', 'max-w-xl') bg-white shadow-2xl">

            <a href="{{ $modalBack ?? route('admin.dashboard') }}" class="absolute right-5 top-5 z-10 text-secondary-shade/50 transition hover:text-primary" aria-label="Fermer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </a>

            <div class="max-h-[85vh] overflow-y-auto p-8 sm:p-10">

                {{-- La bannière de confirmation du layout admin est masquée derrière cette modale
                     (position fixed plein écran) : on la réaffiche ici pour qu'elle reste visible. --}}
                @if(session('status'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start justify-between gap-4 border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
                        <span>{{ session('status') }}</span>
                        <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-green-700/60 transition hover:text-green-700 dark:text-green-400/60 dark:hover:text-green-400">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @yield('modal')
            </div>

        </div>
    </div>

</div>

@endsection
