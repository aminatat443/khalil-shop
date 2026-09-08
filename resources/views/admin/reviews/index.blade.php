@extends('layouts.admin')

@section('title', 'Avis clients')

@section('content')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade">Avis clients</h1>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher par produit ou client…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-72 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 bg-white p-6">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">
        En attente de modération <span class="text-grey">({{ $pending->count() }})</span>
    </h2>

    <div class="mt-4 divide-y divide-secondary-shade/10">
        @forelse($pending as $review)
            <div class="flex items-start justify-between gap-4 py-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-secondary-shade">{{ $review->user->name }}</p>
                        <span class="text-xs text-grey">— {{ $review->product->name }}</span>
                        <span class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-[10px] {{ $i <= $review->rating ? 'text-primary' : 'text-grey-tint' }}"></i>
                            @endfor
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="mt-1.5 text-sm text-grey">{{ $review->comment }}</p>
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
                        <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-grey hover:text-secondary-shade">Rejeter</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="py-4 text-sm text-grey">Aucun avis en attente.</p>
        @endforelse
    </div>
</div>

<div class="mt-6 bg-white p-6">
    <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Publiés récemment</h2>

    <div class="mt-4 divide-y divide-secondary-shade/10">
        @forelse($approved as $review)
            <div class="flex items-start justify-between gap-4 py-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-secondary-shade">{{ $review->user->name }}</p>
                        <span class="text-xs text-grey">— {{ $review->product->name }}</span>
                        <span class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-[10px] {{ $i <= $review->rating ? 'text-primary' : 'text-grey-tint' }}"></i>
                            @endfor
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="mt-1.5 text-sm text-grey">{{ $review->comment }}</p>
                    @endif
                </div>
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Supprimer cet avis ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="shrink-0 text-xs font-semibold uppercase tracking-[0.1em] text-grey hover:text-primary">Supprimer</button>
                </form>
            </div>
        @empty
            <p class="py-4 text-sm text-grey">Aucun avis publié pour le moment.</p>
        @endforelse
    </div>
</div>

@endsection
