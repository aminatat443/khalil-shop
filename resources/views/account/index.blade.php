@extends('layouts.app')

@section('title', 'Mon profil — KhalilShop')

@section('content')
<div class="mx-auto max-w-3xl px-6 py-16 sm:px-10">

    <h1 class="font-display text-4xl font-normal italic text-secondary-shade">Mon profil</h1>

    <div class="mt-10 grid gap-6 sm:grid-cols-2">
        <div class="border border-secondary-shade/10 p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Nom</p>
            <p class="mt-1 text-sm text-secondary-shade">{{ $user->name }}</p>
        </div>
        <div class="border border-secondary-shade/10 p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-grey">Email</p>
            <p class="mt-1 text-sm text-secondary-shade">{{ $user->email }}</p>
        </div>
    </div>

    {{-- Le staff n'achète pas sur la boutique, pas besoin d'adresse de livraison. --}}
    @unless($user->isGestionnaire())
        <div id="adresse" class="mt-10 scroll-mt-24">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Adresse de livraison</h2>
            <p class="mt-2 text-sm text-grey">
                Enregistrez votre adresse une fois pour toutes : elle sera proposée automatiquement à chaque commande, pour aller plus vite.
            </p>

            <form
                action="{{ route('account.address.update') }}"
                method="POST"
                class="mt-6 max-w-xl space-y-6 border border-secondary-shade/10 p-6"
                x-data="{
                    locating: false,
                    locationError: null,
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
                                    // La ville retient l'échelon le plus précis (quartier/commune).
                                    const city = addr.suburb || addr.neighbourhood || addr.quarter || addr.city_district
                                        || addr.city || addr.town || addr.village || addr.county || '';
                                    // Localisation précise : numéro de rue si disponible.
                                    const street = [addr.house_number, addr.road].filter(Boolean).join(' ');

                                    if (this.$refs.regionInput && region) this.$refs.regionInput.value = region;
                                    if (this.$refs.cityInput && city) this.$refs.cityInput.value = city;
                                    if (this.$refs.addressInput && street) this.$refs.addressInput.value = street;

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
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Nom complet</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $defaultAddress->full_name ?? $user->name) }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Téléphone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $defaultAddress->phone ?? '') }}" required placeholder="77 000 00 00" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                </div>

                <div>
                    <button
                        type="button"
                        @click="detectLocation()"
                        :disabled="locating"
                        class="inline-flex items-center gap-2 border border-secondary-shade/20 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:border-secondary-shade disabled:opacity-50"
                    >
                        <i class="fa-solid" :class="locating ? 'fa-spinner fa-spin' : 'fa-location-crosshairs'"></i>
                        <span x-text="locating ? 'Localisation…' : 'Utiliser ma position actuelle'"></span>
                    </button>
                    <template x-if="locationError">
                        <p class="mt-2 text-xs text-primary" x-text="locationError"></p>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Région</label>
                        <input x-ref="regionInput" type="text" name="region" value="{{ old('region', $defaultAddress->region ?? '') }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Ville</label>
                        <input x-ref="cityInput" type="text" name="city" value="{{ old('city', $defaultAddress?->quartier ?: ($defaultAddress?->city ?? '')) }}" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Complément d'adresse</label>
                    <input x-ref="addressInput" type="text" name="address" value="{{ old('address', $defaultAddress->address ?? '') }}" placeholder="Numéro de rue, villa, repère…" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Instructions (optionnel)</label>
                    <textarea name="instructions" rows="2" class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">{{ old('instructions', $defaultAddress->instructions ?? '') }}</textarea>
                </div>

                <button type="submit" class="bg-secondary-shade px-8 py-4 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:bg-primary">
                    Enregistrer mon adresse
                </button>
            </form>
        </div>
    @endunless

    {{-- Le staff (Gestionnaire/Admin/Super Admin) n'achète pas sur la boutique : pas d'historique
         de commandes pour ces rôles, seul le profil est pertinent. --}}
    @unless($user->isGestionnaire())
        <div class="mt-10 flex items-center justify-between">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary-shade">Commandes récentes</h2>
            <a href="{{ route('account.orders') }}" class="text-xs font-semibold text-primary hover:underline">Tout voir</a>
        </div>

        <div class="mt-4 divide-y divide-secondary-shade/10 border-t border-secondary-shade/10">
            @forelse($recentOrders as $order)
                <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between py-4 text-sm hover:text-primary">
                    <span>
                        <span class="font-medium text-secondary-shade">{{ $order->order_number }}</span>
                        <span class="ml-2 text-xs text-grey">{{ $order->created_at->format('d/m/Y') }}</span>
                    </span>
                    <span class="text-xs font-medium text-secondary-shade">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </a>
            @empty
                <p class="py-6 text-sm text-grey">Aucune commande pour le moment.</p>
            @endforelse
        </div>
    @endunless

    {{-- Suppression du compte --}}
    <div class="mt-16 border border-primary/20 p-6" x-data="{ confirming: false }">
        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">Zone dangereuse</h2>
        <p class="mt-2 text-sm text-grey">
            La suppression de votre compte est définitive et ne peut pas être annulée.
        </p>

        <button type="button" x-show="! confirming" @click="confirming = true" class="mt-4 text-xs font-semibold uppercase tracking-[0.1em] text-primary hover:underline">
            Supprimer mon compte
        </button>

        <form x-show="confirming" x-cloak action="{{ route('account.destroy') }}" method="POST" class="mt-4 max-w-sm space-y-4">
            @csrf
            @method('DELETE')
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade">Confirmez avec votre mot de passe</label>
                <input type="password" name="password" required class="w-full border-b border-secondary-shade/20 bg-transparent py-2 text-sm text-secondary-shade outline-none focus:border-primary">
            </div>
            <div class="flex gap-4">
                <button type="button" @click="confirming = false" class="px-6 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-secondary-shade transition hover:text-primary">Annuler</button>
                <button type="submit" class="bg-primary px-6 py-3 text-xs font-semibold uppercase tracking-[0.1em] text-white transition hover:bg-primary-shade">Supprimer définitivement</button>
            </div>
        </form>
    </div>

</div>
@endsection
