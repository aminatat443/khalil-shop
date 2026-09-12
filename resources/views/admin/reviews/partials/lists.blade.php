<div class="bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">
        En attente de modération <span class="text-grey dark:text-white/40">({{ $pending->count() }})</span>
    </h2>

    <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
        @forelse($pending as $review)
            <div class="flex items-start justify-between gap-4 py-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-secondary-shade dark:text-white">{{ $review->user->name }}</p>
                        <span class="text-xs text-grey dark:text-white/40">— {{ $review->product->name }}</span>
                        <span class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-[10px] {{ $i <= $review->rating ? 'text-primary' : 'text-grey-tint dark:text-white/15' }}"></i>
                            @endfor
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="mt-1.5 text-sm text-grey dark:text-white/50">{{ $review->comment }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 gap-3">
                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-primary hover:underline">Publier</button>
                    </form>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Supprimer cet avis ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-grey hover:text-secondary-shade dark:text-white/40 dark:hover:text-white">Rejeter</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="py-4 text-sm text-grey dark:text-white/40">Aucun avis en attente.</p>
        @endforelse
    </div>
</div>

<div class="mt-6 bg-white p-6 shadow-sm ring-1 ring-secondary-shade/5 dark:bg-[#16201f] dark:ring-white/5">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Publiés récemment</h2>

    <div class="mt-4 divide-y divide-secondary-shade/10 dark:divide-white/10">
        @forelse($approved as $review)
            <div class="flex items-start justify-between gap-4 py-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-secondary-shade dark:text-white">{{ $review->user->name }}</p>
                        <span class="text-xs text-grey dark:text-white/40">— {{ $review->product->name }}</span>
                        <span class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-[10px] {{ $i <= $review->rating ? 'text-primary' : 'text-grey-tint dark:text-white/15' }}"></i>
                            @endfor
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="mt-1.5 text-sm text-grey dark:text-white/50">{{ $review->comment }}</p>
                    @endif
                </div>
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Supprimer cet avis ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="shrink-0 text-xs font-semibold uppercase tracking-[0.1em] text-grey hover:text-primary dark:text-white/40">Supprimer</button>
                </form>
            </div>
        @empty
            <p class="py-4 text-sm text-grey dark:text-white/40">Aucun avis publié pour le moment.</p>
        @endforelse
    </div>
</div>
