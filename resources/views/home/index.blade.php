@extends('layouts.app')

@section('title', 'KhalilShop — Mode & Maison')

@section('content')

{{-- Hero principal (section 12) --}}
<section class="mx-auto grid max-w-[1600px] items-center gap-12 px-6 py-14 sm:px-10 md:h-[34rem] md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] md:py-0">

    <div>

        <p class="starting:opacity-0 text-xs font-medium uppercase tracking-[0.35em] text-grey opacity-100 transition-all duration-500">
            Nouvelle collection
        </p>

        <h1 class="starting:opacity-0 starting:translate-y-4 mt-6 translate-y-0 font-display text-6xl font-normal italic leading-[1.02] text-secondary-shade opacity-100 transition-all duration-700 delay-100 md:text-[5.5rem]">
            Votre style,
            <br>
            votre <span class="text-primary">univers.</span>
        </h1>

        <p class="starting:opacity-0 starting:translate-y-4 mt-5 max-w-sm translate-y-0 text-[15px] leading-7 text-grey opacity-100 transition-all duration-700 delay-200">
            Vêtements, chaussures, accessoires et décoration maison — une expérience shopping pensée pour vous.
        </p>

        <div class="starting:opacity-0 mt-8 flex items-center gap-8 opacity-100 transition-all duration-700 delay-300">
            <a
                href="{{ $universes->first() ? route('catalog.show', $universes->first()) : '#' }}"
                class="bg-secondary-shade px-9 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary"
            >
                Découvrir la collection
            </a>

            <a
                href="{{ route('search', ['nouveautes' => 1]) }}"
                class="group text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade"
            >
                Voir les nouveautés
                <span class="mt-1 block h-px w-0 bg-secondary-shade transition-all duration-300 group-hover:w-full"></span>
            </a>
        </div>

    </div>


    <div class="relative hidden h-[26rem] md:block md:h-full">

        @if($heroSlides->isNotEmpty())
            <div
                x-data="{
                    slides: {{ $heroSlides->toJson() }},
                    active: 0,
                    timer: null,
                    next() { this.active = (this.active + 1) % this.slides.length },
                    prev() { this.active = (this.active - 1 + this.slides.length) % this.slides.length },
                    start() { this.timer = setInterval(() => this.next(), 5000) },
                    stop() { clearInterval(this.timer) },
                }"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                class="relative h-full overflow-hidden"
            >
                <template x-for="(slide, i) in slides" :key="i">
                    <a
                        :href="slide.url"
                        x-show="active === i"
                        x-transition:enter="transition ease-out duration-1000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 block"
                    >
                        <img :src="slide.image" :alt="slide.name" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-transparent to-transparent"></div>
                    </a>
                </template>

                {{-- Prix, en haut à gauche sur l'image --}}
                <div class="pointer-events-none absolute left-6 top-6 bg-white px-4 py-2.5">
                    <p class="text-xs font-medium text-secondary-shade" x-text="slides[active]?.name"></p>
                    <p class="font-display text-sm italic text-primary" x-text="new Intl.NumberFormat('fr-FR').format(slides[active]?.price ?? 0) + ' FCFA'"></p>
                </div>

                {{-- Flèches --}}
                <button
                    @click.prevent="prev()"
                    class="absolute left-5 top-1/2 -translate-y-1/2 text-lg text-white transition hover:opacity-60"
                    aria-label="Image précédente"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button
                    @click.prevent="next()"
                    class="absolute right-5 top-1/2 -translate-y-1/2 text-lg text-white transition hover:opacity-60"
                    aria-label="Image suivante"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

                {{-- Points de navigation --}}
                <div class="absolute bottom-5 left-1/2 flex -translate-x-1/2 gap-2">
                    <template x-for="(slide, i) in slides" :key="i">
                        <button
                            @click.prevent="active = i"
                            :class="active === i ? 'w-6 bg-white' : 'w-2 bg-white/50'"
                            class="h-[3px] transition-all duration-300"
                            :aria-label="'Aller à l\'image ' + (i + 1)"
                        ></button>
                    </template>
                </div>
            </div>
        @else
            <div class="h-full bg-primary-tint"></div>
        @endif

    </div>

</section>


{{-- Section catégories (section 13) --}}
@if($universes->isNotEmpty())
<section class="border-t border-secondary-shade/10">
    <div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">

        <p class="text-xs font-medium uppercase tracking-[0.35em] text-grey">Univers</p>
        <h2 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">Explorez nos collections</h2>

        @php
            $universeIcons = [
                'femme' => 'fa-person-dress',
                'homme' => 'fa-shirt',
                'chaussures' => 'fa-shoe-prints',
                'accessoires' => 'fa-gem',
                'maison-decoration' => 'fa-house-chimney-window',
            ];
        @endphp

        <div class="mt-10 grid grid-cols-2 gap-px bg-secondary-shade/10 sm:grid-cols-3 md:grid-cols-5">
            @foreach($universes as $universe)
                <a
                    href="{{ route('catalog.show', $universe) }}"
                    class="group flex aspect-[3/4] flex-col items-center justify-center gap-5 bg-white p-6 text-center transition-colors duration-300 hover:bg-primary-tint/40"
                >
                    <i class="fa-solid {{ $universeIcons[$universe->slug] ?? 'fa-star' }} text-2xl text-secondary-shade/30 transition group-hover:text-primary"></i>
                    <span class="font-display text-lg italic text-secondary-shade">{{ $universe->name }}</span>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endif


{{-- Section Nouveautés (section 14) --}}
@if($newProducts->isNotEmpty())
<section class="border-t border-secondary-shade/10">
    <div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.35em] text-grey">Fraîchement arrivé</p>
                <h2 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">Nouveautés</h2>
            </div>
            <a href="{{ route('search', ['nouveautes' => 1]) }}" class="group text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
                Tout voir
                <span class="mt-1 block h-px w-0 bg-secondary-shade transition-all duration-300 group-hover:w-full"></span>
            </a>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($newProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- Sections éditoriales par univers (sections 15 à 18) --}}
@foreach($universes as $index => $universe)
    @php($items = $editorials[$universe->slug] ?? collect())
    @continue($items->isEmpty())

    <section class="border-t border-secondary-shade/10">
        <div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.35em] text-grey">Collection</p>
                    <h2 class="mt-3 font-display text-4xl font-normal italic text-secondary-shade">{{ $universe->name }}</h2>
                </div>
                <a href="{{ route('catalog.show', $universe) }}" class="group text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
                    Découvrir {{ $universe->name }}
                    <span class="mt-1 block h-px w-0 bg-secondary-shade transition-all duration-300 group-hover:w-full"></span>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($items as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endforeach


{{-- Section Promotions (section 19) --}}
@if($promoProducts->isNotEmpty())
<section class="border-t border-secondary-shade/10">
    <div class="mx-auto max-w-[1600px] px-6 py-16 sm:px-10">

        <div class="mb-10 bg-primary-tint px-10 py-12 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-primary-shade">Sale</p>
            <p class="mt-3 font-display text-6xl font-normal italic text-secondary-shade">Jusqu'à -50%</p>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($promoProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
