@extends('layouts.app')

@section('title', 'Commande — KhalilShop')

@section('content')
<div
    class="mx-auto max-w-2xl px-6 py-16 sm:px-10"
    x-data="{
        step: {{ $errors->has('payment_method') || $errors->has('coupon_code') ? 3 : (auth()->check() && $defaultAddress && $matchedDelivery ? 3 : (auth()->check() && $defaultAddress ? 2 : 1)) }},
        deliveryId: {{ old('delivery_id', $matchedDelivery->id ?? 'null') }},
        deliveryFee: {{ old('delivery_id') ? ($deliveries->firstWhere('id', old('delivery_id'))?->fee ?? 0) : ($matchedDelivery->fee ?? 0) }},
        zones: @js($deliveries->map(fn ($d) => ['id' => $d->id, 'zone' => $d->zone, 'fee' => $d->fee])->values()),
        locating: false,
        locationError: null,
        next() { if (this.step < 4) this.step++; window.scrollTo({ top: 0, behavior: 'smooth' }); },
        prev() { if (this.step > 1) this.step--; window.scrollTo({ top: 0, behavior: 'smooth' }); },
        matchZone(city, region) {
            const haystack = (city + ' ' + region).toLowerCase();
            return this.zones.find((z) => z.zone !== 'Dakar' && haystack.includes(z.zone.toLowerCase()))
                ?? this.zones.find((z) => haystack.includes(z.zone.toLowerCase()));
        },
        detectLocation() {
            this.locationError = null;
            if (! navigator.geolocation) {
                this.locationError = 'La géolocalisation n\'est pas disponible sur cet appareil.';
                return;
            }
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    try {
                        const { latitude, longitude } = pos.coords;
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}&addressdetails=1&zoom=18&accept-language=fr`);
                        const data = await response.json();
                        const addr = data.address || {};
                        const region = addr.state || addr.region || '';
                        // La ville retient l'échelon le plus précis (quartier/commune),
                        // bien plus utile qu'un simple 'Dakar' pour la livraison.
                        const city = addr.suburb || addr.neighbourhood || addr.quarter || addr.city_district
                            || addr.city || addr.town || addr.village || addr.county || '';
                        // Localisation précise : numéro de rue si disponible.
                        const street = [addr.house_number, addr.road].filter(Boolean).join(' ');

                        if (this.$refs.regionInput && region) this.$refs.regionInput.value = region;
                        if (this.$refs.cityInput && city) this.$refs.cityInput.value = city;
                        if (this.$refs.addressInput && street) this.$refs.addressInput.value = street;

                        // Dès que l'adresse est déterminée automatiquement, on affiche
                        // immédiatement le tarif de livraison correspondant.
                        const match = this.matchZone(city, region);
                        if (match) {
                            this.deliveryId = match.id;
                            this.deliveryFee = match.fee;
                        }

                        if (! region && ! city) {
                            this.locationError = 'Adresse introuvable à partir de votre position. Renseignez-la manuellement.';
                        }
                    } catch (e) {
                        this.locationError = 'Impossible de déterminer votre adresse. Renseignez-la manuellement.';
                    } finally {
                        this.locating = false;
                    }
                },
                () => {
                    this.locationError = 'Localisation refusée ou indisponible. Renseignez votre adresse manuellement.';
                    this.locating = false;
                },
                { timeout: 10000 }
            );
        },
    }"
>
    <h1 class="font-display text-4xl font-normal italic text-secondary-shade">Finaliser ma commande</h1>

    {{-- Fil de progression (section 34 du cahier des charges) --}}
    <div class="mt-8 flex items-center">
        <template x-for="n in 4" :key="n">
            <div class="flex items-center">
                <span
                    :class="step >= n ? 'bg-secondary-shade text-white' : 'bg-grey-tint text-grey'"
                    class="flex h-7 w-7 shrink-0 items-center justify-center text-xs font-semibold"
                    x-text="n"
                ></span>
                <div x-show="n < 4" :class="step > n ? 'bg-secondary-shade' : 'bg-grey-tint'" class="h-px w-10 sm:w-16"></div>
            </div>
        </template>
    </div>

    @if($errors->any())
        <div class="mt-6 border border-primary/30 bg-primary-tint px-5 py-4 text-sm text-primary-shade">
            <ul class="list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="mt-10">
        @csrf

        {{-- Étape 1 — Informations personnelles (nom/email déjà connus si connecté, section 34) --}}
        <div x-show="step === 1">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-grey">Étape 1 — {{ auth()->check() ? 'Coordonnées' : 'Informations personnelles' }}</h2>

            <div class="mt-6 space-y-6">
                @auth
                    <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                @else
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Nom complet</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                @endauth

                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Téléphone</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone', $defaultAddress->phone ?? '') }}" required placeholder="77 000 00 00" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                </div>

                @auth
                    <input type="hidden" name="customer_email" value="{{ auth()->user()->email }}">
                @else
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                @endauth
            </div>

            <button type="button" @click="next()" class="mt-10 bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                Continuer
            </button>
        </div>

        {{-- Étape 2 — Adresse et livraison (combinées) --}}
        <div x-show="step === 2" x-cloak>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-grey">Étape 2 — Adresse et livraison</h2>

            <button
                type="button"
                @click="detectLocation()"
                :disabled="locating"
                class="mt-4 inline-flex items-center gap-2 border border-secondary-shade/20 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:border-secondary-shade disabled:opacity-50"
            >
                <i class="fa-solid" :class="locating ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
                <span x-text="locating ? 'Localisation…' : 'Utiliser ma position actuelle'"></span>
            </button>
            <template x-if="locationError">
                <p class="mt-2 text-xs text-primary" x-text="locationError"></p>
            </template>

            <div class="mt-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Région</label>
                        <input
                            x-ref="regionInput"
                            type="text"
                            name="delivery_region"
                            value="{{ old('delivery_region', $defaultAddress->region ?? '') }}"
                            @input="const m = matchZone($refs.cityInput.value, $event.target.value); if (m) { deliveryId = m.id; deliveryFee = m.fee; }"
                            required
                            class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary"
                        >
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Ville</label>
                        <input
                            x-ref="cityInput"
                            type="text"
                            name="delivery_city"
                            value="{{ old('delivery_city', $defaultAddress?->quartier ?: ($defaultAddress?->city ?? '')) }}"
                            @input="const m = matchZone($event.target.value, $refs.regionInput.value); if (m) { deliveryId = m.id; deliveryFee = m.fee; }"
                            required
                            class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary"
                        >
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Complément d'adresse</label>
                    <input x-ref="addressInput" type="text" name="delivery_address" value="{{ old('delivery_address', $defaultAddress->address ?? '') }}" placeholder="Numéro de rue, villa, repère…" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Instructions (optionnel)</label>
                    <textarea name="delivery_instructions" rows="2" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">{{ old('delivery_instructions', $defaultAddress->instructions ?? '') }}</textarea>
                </div>
            </div>

            {{-- Zone de livraison — le tarif s'affiche et se met à jour automatiquement dès que
                 l'adresse est renseignée (manuellement ou via la géolocalisation). --}}
            <h3 class="mt-10 text-xs font-semibold uppercase tracking-[0.2em] text-grey">Zone de livraison</h3>

            <div class="mt-4">
                <select
                    name="delivery_id"
                    x-model.number="deliveryId"
                    @change="const d = zones.find((z) => z.id === deliveryId); if (d) deliveryFee = d.fee;"
                    required
                    class="w-full border-b border-secondary-shade/20 bg-transparent py-2.5 text-sm text-secondary-shade outline-none focus:border-primary"
                >
                    <option value="" disabled>— Choisir une zone —</option>
                    @forelse($deliveries as $delivery)
                        <option value="{{ $delivery->id }}">
                            {{ $delivery->zone }} — {{ $delivery->estimated_days }}{{ $delivery->fee > 0 ? ' — '.number_format($delivery->fee, 0, ',', ' ').' FCFA' : '' }}
                        </option>
                    @empty
                        <option value="" disabled>Aucune zone de livraison configurée pour le moment.</option>
                    @endforelse
                </select>
            </div>

            {{-- Récapitulatif du tarif, mis à jour en temps réel --}}
            <div class="mt-4 flex items-center justify-between border border-secondary-shade/10 bg-grey-tint px-5 py-4">
                <span class="text-sm font-medium uppercase tracking-[0.08em] text-secondary-shade">Frais de livraison</span>
                <span class="text-sm font-semibold text-secondary-shade" x-text="deliveryFee > 0 ? (new Intl.NumberFormat('fr-FR').format(deliveryFee) + ' FCFA') : 'À discuter sur WhatsApp'"></span>
            </div>

            <div class="mt-10 flex gap-4">
                <button type="button" @click="prev()" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Retour</button>
                <button
                    type="button"
                    @click="if (! $refs.regionInput.value.trim() || ! $refs.cityInput.value.trim()) { alert('Merci de renseigner la région et la ville avant de continuer.'); } else { next(); }"
                    class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary"
                >Continuer</button>
            </div>
        </div>

        {{-- Étape 3 — Paiement --}}
        <div x-show="step === 3" x-cloak>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-grey">Étape 3 — Paiement</h2>

            <div class="mt-6 space-y-3">
                <label class="flex cursor-pointer items-center gap-3 border border-secondary-shade/15 px-5 py-4 has-[:checked]:border-secondary-shade">
                    <input type="radio" name="payment_method" value="cod" checked required class="h-4 w-4 text-primary focus:ring-primary">
                    <span class="text-sm text-secondary-shade">Paiement à la livraison</span>
                </label>

                @foreach(['wave' => 'Wave', 'orange_money' => 'Orange Money', 'carte' => 'Carte bancaire'] as $value => $label)
                    <label class="flex cursor-not-allowed items-center justify-between border border-secondary-shade/10 px-5 py-4 opacity-40">
                        <span class="flex items-center gap-3">
                            <input type="radio" disabled class="h-4 w-4">
                            <span class="text-sm text-secondary-shade">{{ $label }}</span>
                        </span>
                        <span class="text-xs uppercase tracking-[0.1em] text-grey">Bientôt disponible</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-8">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Code promo (optionnel)</label>
                <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>

            <div class="mt-10 flex gap-4">
                <button type="button" @click="prev()" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Retour</button>
                <button type="button" @click="next()" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">Continuer</button>
            </div>
        </div>

        {{-- Étape 4 — Confirmation --}}
        <div x-show="step === 4" x-cloak>
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-grey">Étape 4 — Récapitulatif</h2>

            <div class="mt-6 divide-y divide-secondary-shade/10 border-y border-secondary-shade/10">
                <template x-for="item in $store.cart.items" :key="item.product_id + ':' + item.variant_id">
                    <div class="flex items-center justify-between py-4">
                        <div>
                            <p class="text-sm font-medium text-secondary-shade" x-text="item.name"></p>
                            <template x-if="item.variant_label">
                                <p class="text-xs text-grey" x-text="item.variant_label"></p>
                            </template>
                            <p class="text-xs text-grey" x-text="'Qté : ' + item.quantity"></p>
                        </div>
                        <p class="text-sm font-medium text-secondary-shade" x-text="new Intl.NumberFormat('fr-FR').format(item.subtotal) + ' FCFA'"></p>
                    </div>
                </template>
            </div>

            <div class="mt-4 space-y-1.5 text-sm text-grey">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span x-text="new Intl.NumberFormat('fr-FR').format($store.cart.subtotal) + ' FCFA'"></span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison</span>
                    <span x-text="deliveryFee > 0 ? (new Intl.NumberFormat('fr-FR').format(deliveryFee) + ' FCFA') : 'À discuter sur WhatsApp'"></span>
                </div>
            </div>

            <div class="mt-3 flex justify-between border-t border-secondary-shade/10 pt-3">
                <span class="text-sm font-medium uppercase tracking-[0.1em] text-secondary-shade">Total</span>
                <span class="font-display text-xl italic text-secondary-shade" x-text="new Intl.NumberFormat('fr-FR').format($store.cart.subtotal + deliveryFee) + ' FCFA' + (deliveryFee === 0 ? ' + livraison' : '')"></span>
            </div>

            <p class="mt-4 text-xs text-grey">Paiement à la livraison. Vous recevrez un appel de confirmation avant l'expédition.</p>
            <template x-if="deliveryFee === 0">
                <p class="mt-2 text-xs text-primary">
                    <i class="fa-brands fa-whatsapp mr-1"></i>Nous vous contacterons sur WhatsApp pour confirmer le tarif et le délai de livraison.
                </p>
            </template>

            <div class="mt-10 flex gap-4">
                <button type="button" @click="prev()" class="px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">Retour</button>
                <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">Confirmer la commande</button>
            </div>
        </div>

    </form>
</div>
@endsection
