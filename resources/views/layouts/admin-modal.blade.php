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
                @yield('modal')
            </div>

        </div>
    </div>

</div>

@endsection
