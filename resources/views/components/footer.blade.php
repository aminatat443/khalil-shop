<footer class="border-t border-secondary-shade/10 bg-white text-secondary-shade">

    {{-- Bandeau newsletter --}}
    <div class="border-b border-secondary-shade/10">
        <div class="mx-auto max-w-[1600px] px-6 py-20 text-center sm:px-10">
            <p class="font-display text-3xl font-normal italic text-secondary-shade">Restez inspiré·e</p>
            <p class="mx-auto mt-3 max-w-md text-sm text-grey">
                Recevez nos nouveautés, promotions et inspirations déco directement dans votre boîte mail.
            </p>

            <form class="mx-auto mt-8 flex max-w-sm items-end gap-4">
                <input
                    type="email"
                    placeholder="Votre email"
                    class="w-full border-b border-secondary-shade/25 bg-transparent py-2 text-sm text-secondary-shade outline-none placeholder:text-grey/50 focus:border-primary"
                >
                <button type="submit" class="shrink-0 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-shade transition hover:text-primary">
                    S'inscrire
                </button>
            </form>
        </div>
    </div>


    <div class="mx-auto grid max-w-[1600px] gap-10 px-6 py-16 sm:px-10 md:grid-cols-5">

        <div>
            <img src="{{ asset('images/Khalil_shop-cropped.svg') }}" alt="KhalilShop" class="h-9 w-auto">

            <p class="mt-4 text-sm leading-6 text-grey">
                Mode, accessoires et décoration pour créer
                un style qui vous ressemble.
            </p>

            <div class="mt-6 flex gap-5 text-secondary-shade/60">
                <a href="#" aria-label="Instagram" class="transition hover:text-primary">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" aria-label="Facebook" class="transition hover:text-primary">
                    <i class="fa-brands fa-facebook"></i>
                </a>
                <a href="#" aria-label="TikTok" class="transition hover:text-primary">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                <a href="#" aria-label="WhatsApp" class="transition hover:text-primary">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </div>
        </div>


        <div>
            <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-grey">Boutique</h3>

            <ul class="mt-5 space-y-3 text-sm text-secondary-shade">
                @foreach(($navCategories ?? [])->take(4) as $universe)
                    <li><a href="{{ route('catalog.show', $universe) }}" class="transition hover:text-primary">{{ $universe->name }}</a></li>
                @endforeach
            </ul>
        </div>


        <div>
            <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-grey">Découvrir</h3>

            <ul class="mt-5 space-y-3 text-sm text-secondary-shade">
                <li><a href="{{ route('search', ['nouveautes' => 1]) }}" class="transition hover:text-primary">Nouveautés</a></li>
                <li><a href="{{ route('search', ['promotions' => 1]) }}" class="transition hover:text-primary">Promotions</a></li>
            </ul>
        </div>


        <div class="md:col-span-2">
            <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-grey">Service client</h3>

            <ul class="mt-5 space-y-3 text-sm text-secondary-shade">
                <li>Contact</li>
                <li>Livraison</li>
                <li>Retours</li>
                <li>FAQ</li>
            </ul>
        </div>

    </div>


    <div class="border-t border-secondary-shade/10">
        <div class="mx-auto max-w-[1600px] px-6 py-6 text-center text-xs text-grey sm:px-10">
            © {{ date('Y') }} KhalilShop. Tous droits réservés.
        </div>
    </div>

</footer>
