{{-- Notifications flash (toasts) — même style que le back-office : auto-masquées après
     quelques secondes, fermables à tout moment, flottantes en haut à droite. --}}
@if(session('status') || session('error') || $errors->any())
    <div class="pointer-events-none fixed inset-x-4 top-4 z-[100] flex flex-col items-center gap-3 sm:inset-x-auto sm:right-4 sm:items-end" aria-live="polite">
        @if(session('status'))
            <div
                x-data="{ show: false }"
                x-init="setTimeout(() => show = true, 30); setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-x-4 sm:translate-y-0" x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 bg-white px-5 py-4 text-sm text-secondary-shade shadow-xl ring-1 ring-black/5"
            >
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-check text-[11px]"></i>
                </span>
                <span class="flex-1">{{ session('status') }}</span>
                <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-secondary-shade/40 transition hover:text-secondary-shade">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div
                x-data="{ show: false }"
                x-init="setTimeout(() => show = true, 30); setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-x-4 sm:translate-y-0" x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 bg-white px-5 py-4 text-sm text-secondary-shade shadow-xl ring-1 ring-black/5"
            >
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                </span>
                <span class="flex-1">{{ session('error') }}</span>
                <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-secondary-shade/40 transition hover:text-secondary-shade">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div
                x-data="{ show: false }"
                x-init="setTimeout(() => show = true, 30)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-x-4 sm:translate-y-0" x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 bg-white px-5 py-4 text-sm text-secondary-shade shadow-xl ring-1 ring-black/5"
            >
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                </span>
                <ul class="flex-1 list-disc space-y-1 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-secondary-shade/40 transition hover:text-secondary-shade">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>
@endif
