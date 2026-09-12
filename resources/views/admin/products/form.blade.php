@extends('layouts.admin-modal')

@php($modalBack = route('admin.products.index'))

@php($isDraft = $product->name === 'Nouveau produit')

@section('title', $isDraft ? 'Nouveau produit' : 'Modifier le produit')

@section('modal-width', 'max-w-3xl')

@section('modal')

<h1 class="font-display text-3xl font-normal italic text-secondary-shade dark:text-white">
    {{ $isDraft ? 'Nouveau produit' : 'Modifier « '.$product->name.' »' }}
</h1>
@if($isDraft)
    <p class="mt-2 text-sm text-grey dark:text-white/40">Ce produit reste masqué de la boutique (inactif) tant que vous ne l'activez pas ci-dessous.</p>
@endif

{{-- Champs de base (section 42 du cahier des charges) — enregistrement automatique, aucun bouton --}}
<form
    action="{{ route('admin.products.update', $product) }}"
    method="POST"
    enctype="multipart/form-data"
    class="mt-8 space-y-6"
    x-data="{
        status: 'idle',
        async saveForm(form) {
            this.status = 'saving';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                    body: new FormData(form),
                });
                this.status = response.ok ? 'saved' : 'error';
            } catch (e) {
                this.status = 'error';
            } finally {
                setTimeout(() => { this.status = 'idle'; }, 2000);
            }
        },
    }"
>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Nom</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Catégorie</label>
            <select name="category_id" required @change="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
                <option value="">— Choisir —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->parent?->name }} — {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Prix (FCFA)</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Ancien prix (optionnel)</label>
            <input type="number" name="old_price" value="{{ old('old_price', $product->old_price) }}" min="0" @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">
                Stock
                @if($product->variants->isNotEmpty() ?? false)
                    <span class="normal-case text-grey">(ignoré — géré par variante ci-dessous)</span>
                @endif
            </label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0" @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div>
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Matière (optionnel)</label>
            <input type="text" name="material" value="{{ old('material', $product->material) }}" @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
        </div>

        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Description</label>
            <textarea name="description" rows="4" @blur="saveForm($event.target.form)" class="w-full border border-secondary-shade/15 bg-white px-3.5 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">{{ old('description', $product->description) }}</textarea>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-x-8 gap-y-3 pt-2">
        @foreach(['is_new' => 'Nouveauté', 'is_promo' => 'Promotion', 'is_featured' => 'Produit vedette', 'is_active' => 'Actif'] as $field => $label)
            <label class="flex items-center gap-2.5 text-sm text-secondary-shade dark:text-white">
                <input
                    type="checkbox"
                    name="{{ $field }}"
                    value="1"
                    @checked(old($field, $product->{$field} ?? ($field === 'is_active')))
                    @change="
                        status = 'saving';
                        fetch('{{ route('admin.products.toggle', [$product, $field]) }}', {
                            method: 'POST',
                            headers: { Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                        }).then((r) => status = r.ok ? 'saved' : 'error').finally(() => setTimeout(() => status = 'idle', 2000));
                    "
                    class="h-4 w-4 text-primary focus:ring-primary"
                >
                {{ $label }}
            </label>
        @endforeach

        <span class="flex items-center gap-1.5 text-xs text-grey dark:text-white/40">
            <i x-show="status === 'saving'" x-cloak class="fa-solid fa-circle-notch fa-spin"></i>
            <span x-show="status === 'saving'" x-cloak>Enregistrement…</span>
            <i x-show="status === 'saved'" x-cloak class="fa-solid fa-check text-primary"></i>
            <span x-show="status === 'saved'" x-cloak class="text-primary">Enregistré</span>
            <i x-show="status === 'error'" x-cloak class="fa-solid fa-triangle-exclamation text-red-500"></i>
            <span x-show="status === 'error'" x-cloak class="text-red-500">Échec de l'enregistrement</span>
        </span>
    </div>
</form>

<a href="{{ route('admin.products.index') }}" class="mt-6 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:text-primary dark:text-white/70">
    <i class="fa-solid fa-arrow-left text-[10px]"></i>Retour aux produits
</a>

    @can('delete', $product)
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="mt-4" onsubmit="return confirm('Supprimer définitivement ce produit ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold uppercase tracking-[0.15em] text-primary hover:underline">Supprimer ce produit</button>
        </form>
    @endcan

    {{-- Images (section 49 du cahier des charges) --}}
    <div class="mt-10 border-t border-secondary-shade/10 pt-8">
        <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
            <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-images text-[10px]"></i></span>
            Images
        </h2>

        <div
            x-data="{
                dragId: null,
                uploadError: null,
                async persist() {
                    const ids = [...this.$refs.grid.querySelectorAll('[data-image-id]')].map(el => el.dataset.imageId);
                    await fetch('{{ route('admin.products.images.reorder', $product) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                        body: JSON.stringify({ ids }),
                    });
                },
                async upload(e) {
                    this.uploadError = null;
                    const form = e.target;
                    const body = new FormData(form);
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: { Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                            body,
                        });
                        const data = await response.json();
                        if (!response.ok) {
                            this.uploadError = Object.values(data.errors ?? {})[0]?.[0] ?? 'Envoi impossible.';
                            return;
                        }
                        this.$refs.grid.innerHTML = data.html;
                    } catch (err) {
                        this.uploadError = 'Envoi impossible.';
                    }
                },
                async setPrimary(e) {
                    const response = await fetch(e.target.action, {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                        body: new FormData(e.target),
                    });
                    if (response.ok) {
                        this.$refs.grid.innerHTML = (await response.json()).html;
                    }
                },
                async destroyImage(e) {
                    if (! confirm('Supprimer cette image ?')) return;
                    const response = await fetch(e.target.action, {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                        body: new FormData(e.target),
                    });
                    if (response.ok) {
                        this.$refs.grid.innerHTML = (await response.json()).html;
                    }
                },
            }"
        >
            <div x-ref="grid" class="mt-5 grid grid-cols-4 gap-4">
                @include('admin.products.partials.image-grid')
            </div>
            <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-2 text-xs text-primary"></p>
            <p class="mt-2 text-xs text-grey dark:text-white/40">Glissez une vignette pour changer l'ordre — la première est la photo principale. Déposez de nouvelles photos sur la tuile "+", l'envoi se lance automatiquement.</p>
        </div>
    </div>

    {{-- Variantes (sections 27, 41, 45 du cahier des charges) --}}
    <div class="mt-8 border-t border-secondary-shade/10 pt-8">
        <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade dark:text-white/70">
            <span class="flex h-6 w-6 items-center justify-center bg-primary-tint text-primary dark:bg-primary/15"><i class="fa-solid fa-ruler-combined text-[10px]"></i></span>
            Variantes (taille / couleur / stock)
        </h2>

        <div class="mt-5 divide-y divide-secondary-shade/10 dark:divide-white/10">
            @forelse($product->variants as $variant)
                <div class="flex flex-col gap-3 py-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                    <span class="min-w-0 text-secondary-shade dark:text-white sm:flex-1">
                        {{ $variant->color?->name ?? '—' }} / {{ $variant->size?->name ?? '—' }}
                        <span class="block text-xs text-grey dark:text-white/40 sm:inline">SKU {{ $variant->sku }}</span>
                    </span>
                    <div class="flex items-center justify-between gap-3 sm:shrink-0 sm:justify-start">
                        <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="color_id" value="{{ $variant->color_id }}">
                            <input type="hidden" name="size_id" value="{{ $variant->size_id }}">
                            <input type="hidden" name="sku" value="{{ $variant->sku }}">
                            <input type="number" name="stock" value="{{ $variant->stock }}" min="0" class="w-20 border border-secondary-shade/15 bg-white px-3.5 py-1 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white">
                            <button type="submit" class="whitespace-nowrap text-xs text-secondary-shade hover:text-primary dark:text-white/70">Mettre à jour</button>
                        </form>
                        <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" onsubmit="return confirm('Supprimer cette variante ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="whitespace-nowrap text-xs text-primary hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-grey dark:text-white/40">Aucune variante — le champ "Stock" ci-dessus s'applique directement au produit.</p>
            @endforelse
        </div>

        <form
            action="{{ route('admin.products.variants.store', $product) }}"
            method="POST"
            class="mt-6 space-y-5 border border-dashed border-secondary-shade/20 p-5 dark:border-white/15"
            x-data="{
                colors: {{ Illuminate\Support\Js::from($colors->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'hex' => $c->hex_code ?? '#cccccc'])) }},
                sizesData: {{ Illuminate\Support\Js::from($sizes->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])) }},
                colorIds: [],
                sizeIds: [],
                stocks: {},
                pickerOpen: false,
                newHex: '#213737',
                newName: '',
                savingColor: false,
                toggleColor(id) {
                    id = String(id);
                    this.colorIds = this.colorIds.includes(id) ? this.colorIds.filter(c => c !== id) : [...this.colorIds, id];
                },
                toggleSize(id) {
                    id = String(id);
                    this.sizeIds = this.sizeIds.includes(id) ? this.sizeIds.filter(s => s !== id) : [...this.sizeIds, id];
                },
                get combosList() {
                    if (! this.colorIds.length && ! this.sizeIds.length) return [];
                    const colors = this.colorIds.length ? this.colorIds : [null];
                    const sizes = this.sizeIds.length ? this.sizeIds : [null];
                    const list = [];
                    for (const c of colors) {
                        for (const s of sizes) {
                            const key = `${c}-${s}`;
                            if (! (key in this.stocks)) this.stocks[key] = '';
                            list.push({ key, colorId: c, sizeId: s });
                        }
                    }
                    return list;
                },
                comboLabel(combo) {
                    const colorName = combo.colorId ? (this.colors.find(c => String(c.id) === combo.colorId)?.name ?? '') : '';
                    const sizeName = combo.sizeId ? (this.sizesData.find(s => String(s.id) === combo.sizeId)?.name ?? '') : '';
                    return [colorName, sizeName].filter(Boolean).join(' / ') || '—';
                },
                async addCustomColor() {
                    if (! this.newName.trim() || this.savingColor) return;
                    this.savingColor = true;
                    try {
                        const response = await fetch('{{ route('admin.colors.store') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': window.csrfToken ? window.csrfToken() : '' },
                            body: JSON.stringify({ name: this.newName.trim(), hex_code: this.newHex }),
                        });
                        const color = await response.json();
                        if (response.ok) {
                            if (! this.colors.some(c => c.id === color.id)) this.colors.push({ id: color.id, name: color.name, hex: color.hex_code });
                            this.toggleColor(color.id);
                            this.newName = '';
                            this.pickerOpen = false;
                        }
                    } finally {
                        this.savingColor = false;
                    }
                },
            }"
        >
            @csrf

            <p class="text-xs text-grey dark:text-white/40"><i class="fa-solid fa-bolt mr-1.5 text-primary"></i>Cochez plusieurs couleurs et/ou tailles : une case de stock apparaît pour chaque combinaison.</p>

            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Couleur</label>
                <template x-for="id in colorIds" :key="id">
                    <input type="hidden" name="color_ids[]" :value="id">
                </template>
                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="color in colors" :key="color.id">
                        <button
                            type="button"
                            @click="toggleColor(color.id)"
                            class="relative h-8 w-8 shrink-0 rounded-full transition"
                            :class="colorIds.includes(String(color.id)) ? 'ring-2 ring-offset-2 ring-secondary-shade dark:ring-white dark:ring-offset-[#16201f]' : 'ring-1 ring-secondary-shade/15 hover:ring-secondary-shade/40 dark:ring-white/20 dark:hover:ring-white/50'"
                            :style="`background-color: ${color.hex}`"
                            :title="color.name"
                            :aria-label="color.name"
                        >
                            <span x-show="colorIds.includes(String(color.id))" x-cloak class="absolute inset-0 flex items-center justify-center text-[10px]" :class="['#ffffff','#fff'].includes(color.hex.toLowerCase()) ? 'text-secondary-shade' : 'text-white'">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </button>
                    </template>

                    {{-- Sélecteur de couleur général : pas limité aux couleurs déjà enregistrées --}}
                    <div class="relative">
                        <button
                            type="button"
                            @click="pickerOpen = ! pickerOpen"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-secondary-shade/50 ring-1 ring-dashed ring-secondary-shade/25 transition hover:text-primary hover:ring-primary/50 dark:text-white/40 dark:ring-white/20"
                            title="Choisir une autre couleur"
                            aria-label="Choisir une autre couleur"
                        >
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>

                        <div
                            x-show="pickerOpen"
                            x-cloak
                            @click.outside="pickerOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute left-0 top-full z-20 mt-2 w-60 space-y-3 border border-secondary-shade/10 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-[#1c2826]"
                        >
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="newHex" class="h-10 w-12 shrink-0 cursor-pointer border border-secondary-shade/15 bg-transparent p-0.5 dark:border-white/10">
                                <input
                                    type="text"
                                    x-model="newName"
                                    placeholder="Nom (ex : Vert sauge)"
                                    @keydown.enter.prevent="addCustomColor()"
                                    class="w-full border border-secondary-shade/15 bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white"
                                >
                            </div>
                            <button
                                type="button"
                                @click="addCustomColor()"
                                :disabled="! newName.trim() || savingColor"
                                class="w-full bg-secondary-shade py-2 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-primary disabled:opacity-40"
                            >
                                <span x-show="! savingColor">Ajouter cette couleur</span>
                                <span x-show="savingColor">Ajout…</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if($sizes->isNotEmpty())
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Taille</label>
                    <template x-for="id in sizeIds" :key="id">
                        <input type="hidden" name="size_ids[]" :value="id">
                    </template>
                    <div class="flex flex-wrap gap-2">
                        @foreach($sizes as $size)
                            <button
                                type="button"
                                @click="toggleSize('{{ $size->id }}')"
                                class="flex items-center gap-1.5 border px-3 py-1.5 text-xs font-medium transition"
                                :class="sizeIds.includes('{{ $size->id }}') ? 'border-secondary-shade bg-secondary-shade text-white dark:border-white dark:bg-white dark:text-secondary-shade' : 'border-secondary-shade/20 text-secondary-shade hover:border-secondary-shade/50 dark:border-white/20 dark:text-white/70'"
                            >
                                <i class="fa-solid fa-check text-[10px]" x-show="sizeIds.includes('{{ $size->id }}')" x-cloak></i>
                                {{ $size->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div x-show="combosList.length" x-cloak>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade dark:text-white/70">Stock par variante</label>
                <div class="grid gap-2 sm:grid-cols-2">
                    <template x-for="(combo, i) in combosList" :key="combo.key">
                        <div class="flex items-center justify-between gap-3 border border-secondary-shade/10 px-3 py-2 dark:border-white/10">
                            <span class="text-xs font-medium text-secondary-shade dark:text-white/80" x-text="comboLabel(combo)"></span>
                            <input type="hidden" :name="`variants[${i}][color_id]`" :value="combo.colorId ?? ''">
                            <input type="hidden" :name="`variants[${i}][size_id]`" :value="combo.sizeId ?? ''">
                            <input
                                type="number"
                                :name="`variants[${i}][stock]`"
                                x-model.number="stocks[combo.key]"
                                min="0"
                                placeholder="Stock"
                                required
                                class="w-24 border border-secondary-shade/15 bg-white px-2.5 py-1.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 dark:border-white/10 dark:bg-white/5 dark:text-white"
                            >
                        </div>
                    </template>
                </div>
            </div>

            <button
                type="submit"
                x-show="combosList.length"
                x-cloak
                class="bg-secondary-shade px-5 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-white shadow-sm transition hover:bg-primary hover:shadow-md"
            >
                <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i>
                Générer <span x-text="`(${combosList.length})`"></span>
            </button>
        </form>
    </div>

@endsection
