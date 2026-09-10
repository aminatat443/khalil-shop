@if(session('email_verified_message'))
    <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center p-6">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click="open = false"
            class="absolute inset-0 bg-secondary-shade/40"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            @click.outside="open = false"
            class="relative w-full max-w-sm bg-white px-10 py-10 text-center"
        >
            <button type="button" @click="open = false" class="absolute right-6 top-6 text-secondary-shade transition hover:text-primary" aria-label="Fermer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-600">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2 class="mt-6 font-display text-2xl italic text-secondary-shade">Email vérifié</h2>
            <p class="mt-3 text-sm text-grey">{{ session('email_verified_message') }}</p>

            <button type="button" @click="open = false" class="mt-8 w-full bg-secondary-shade px-6 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                Continuer
            </button>
        </div>
    </div>
@endif
