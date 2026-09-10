@if(session('status'))
    <div x-data="{ show: true }" x-show="show" class="border-b border-green-200 bg-green-50">
        <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-6 py-3 text-sm text-green-700 sm:px-10">
            <span><i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}</span>
            <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-green-700/60 transition hover:text-green-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
@endif
