@extends('layouts.admin')

@section('title', 'Produits')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-3xl font-normal italic text-secondary-shade">Produits</h1>
    @can('create', App\Models\Product::class)
        <a href="{{ route('admin.products.create') }}" class="bg-secondary-shade px-6 py-3 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
            Ajouter un produit
        </a>
    @endcan
</div>

<form method="GET" x-data class="mt-8 flex flex-wrap items-center gap-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit…" @input.debounce.500ms="$el.form.submit()" @if(request()->filled('q')) autofocus @endif class="w-64 border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
    <select name="category_id" onchange="this.form.submit()" class="border-b border-secondary-shade/20 bg-transparent py-2 text-sm outline-none focus:border-primary">
        <option value="">Toutes les catégories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade hover:text-primary">Filtrer</button>
</form>

<div class="mt-6 overflow-x-auto bg-white">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-secondary-shade/10 text-left text-xs uppercase tracking-[0.1em] text-grey">
                <th class="px-6 py-4 font-medium">Produit</th>
                <th class="px-6 py-4 font-medium">Catégorie</th>
                <th class="px-6 py-4 font-medium">Prix</th>
                <th class="px-6 py-4 font-medium">Stock</th>
                <th class="px-6 py-4 font-medium">Statut</th>
                <th class="px-6 py-4 font-medium"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-secondary-shade/10">
            @forelse($products as $product)
                <tr>
                    <td class="flex items-center gap-3 px-6 py-4">
                        <div class="h-12 w-10 shrink-0 overflow-hidden bg-grey-tint">
                            @if($image = $product->images->first()?->url)
                                <img src="{{ $image }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <span class="font-medium text-secondary-shade">{{ $product->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-grey">{{ $product->category->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-secondary-shade">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-secondary-shade">
                        {{ $product->variants_count > 0 ? $product->variants_count.' variante(s)' : $product->stock }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $product->is_active ? 'text-primary' : 'text-grey' }}">{{ $product->is_active ? 'Actif' : 'Désactivé' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs text-secondary-shade hover:text-primary">Modifier</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-grey">Aucun produit ne correspond à ces critères.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $products->links() }}</div>

@endsection
