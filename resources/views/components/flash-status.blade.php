@if(session('status'))
    <div class="border-b border-secondary-shade/10 bg-grey-tint">
        <div class="mx-auto max-w-[1600px] px-6 py-3 text-sm text-secondary-shade sm:px-10">
            <i class="fa-solid fa-circle-check mr-2 text-primary"></i>{{ session('status') }}
        </div>
    </div>
@endif
