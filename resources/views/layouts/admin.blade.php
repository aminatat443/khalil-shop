<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration') — KhalilShop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300..900&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Appliqué avant le rendu pour éviter un flash clair au chargement d'une page en mode sombre --}}
    <script>
        if (localStorage.getItem('khalilshop-admin-theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-grey-tint text-secondary-shade antialiased dark:bg-[#0f1615] dark:text-[#e7ece9]" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside
            x-cloak
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 flex-col overflow-hidden bg-gradient-to-b from-[#26413f] via-secondary-shade to-[#152423] text-white transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
        >
            {{-- Halo décoratif discret, purement visuel --}}
            <div class="pointer-events-none absolute -left-16 -top-24 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-1/2 h-72 w-72 rounded-full bg-white/[0.03] blur-3xl"></div>

            <div class="relative flex h-20 items-center px-7">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center bg-white/10 ring-1 ring-white/10">
                        <img src="{{ asset('images/Khalil_shop-cropped.svg') }}" alt="KhalilShop" class="h-6 w-auto">
                    </span>
                    <span class="font-display text-lg italic text-white">Admin</span>
                </a>
            </div>

            <nav class="relative flex-1 space-y-6 overflow-y-auto px-4 py-4 text-sm [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @php
                    $groups = [
                        [
                            'label' => 'Principal',
                            'links' => [
                                ['route' => 'admin.dashboard', 'group' => 'admin.dashboard', 'icon' => 'fa-gauge', 'label' => 'Tableau de bord'],
                            ],
                        ],
                        [
                            'label' => 'Catalogue',
                            'links' => [
                                ['route' => 'admin.products.index', 'group' => 'admin.products.*', 'icon' => 'fa-shirt', 'label' => 'Produits'],
                                ['route' => 'admin.categories.index', 'group' => 'admin.categories.*', 'icon' => 'fa-sitemap', 'label' => 'Catégories'],
                            ],
                        ],
                        [
                            'label' => 'Ventes',
                            'links' => [
                                ['route' => 'admin.orders.index', 'group' => 'admin.orders.*', 'icon' => 'fa-bag-shopping', 'label' => 'Commandes'],
                                ['route' => 'admin.finances.index', 'group' => 'admin.finances.*', 'icon' => 'fa-sack-dollar', 'label' => 'Finances'],
                                ['route' => 'admin.invoices.index', 'group' => 'admin.invoices.*', 'icon' => 'fa-file-invoice', 'label' => 'Factures'],
                                ['route' => 'admin.coupons.index', 'group' => 'admin.coupons.*', 'icon' => 'fa-tag', 'label' => 'Codes promo'],
                                ['route' => 'admin.reviews.index', 'group' => 'admin.reviews.*', 'icon' => 'fa-star', 'label' => 'Avis clients'],
                                ['route' => 'admin.returns.index', 'group' => 'admin.returns.*', 'icon' => 'fa-rotate-left', 'label' => 'Retours'],
                            ],
                        ],
                    ];
                @endphp

                @foreach($groups as $group)
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">{{ $group['label'] }}</p>
                        <div class="mt-2 space-y-1">
                            @foreach($group['links'] as $link)
                                @php $active = request()->routeIs($link['group']); @endphp
                                <a
                                    href="{{ route($link['route']) }}"
                                    class="group relative flex items-center gap-3 px-3 py-2.5 transition {{ $active ? 'bg-white/10 text-white ring-1 ring-white/10' : 'text-white/55 hover:bg-white/5 hover:text-white' }}"
                                >
                                    <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-primary transition-all duration-200 {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                                    <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-xs transition {{ $active ? 'text-primary' : 'text-white/40 group-hover:text-white/70' }}"></i>
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @php
                    $gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
                    $secondaryLinks = [];
                    if ($gate->check('create', [App\Models\User::class, App\Enums\Role::Gestionnaire])) {
                        $secondaryLinks[] = ['route' => 'admin.staff.index', 'group' => 'admin.staff.*', 'icon' => 'fa-users', 'label' => 'Équipe'];
                    }
                    if ($gate->check('viewAny', App\Models\User::class)) {
                        $secondaryLinks[] = ['route' => 'admin.users.index', 'group' => 'admin.users.*', 'icon' => 'fa-user-group', 'label' => 'Utilisateurs'];
                    }
                    if ($gate->check('viewAny', App\Models\Campaign::class)) {
                        $secondaryLinks[] = ['route' => 'admin.campaigns.index', 'group' => 'admin.campaigns.*', 'icon' => 'fa-envelope-open-text', 'label' => 'Campagnes email'];
                    }
                    if ($gate->check('viewAny', App\Models\SecurityEvent::class)) {
                        $secondaryLinks[] = ['route' => 'admin.security.index', 'group' => 'admin.security.*', 'icon' => 'fa-shield-halved', 'label' => 'Sécurité'];
                    }
                    if ($gate->check('viewAny', App\Models\Setting::class)) {
                        $secondaryLinks[] = ['route' => 'admin.settings.edit', 'group' => 'admin.settings.*', 'icon' => 'fa-gear', 'label' => 'Configuration'];
                    }
                @endphp

                @if(count($secondaryLinks))
                    <div>
                        <p class="px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">Marketing &amp; système</p>
                        <div class="mt-2 space-y-1">
                            @foreach($secondaryLinks as $link)
                                @php $active = request()->routeIs($link['group']); @endphp
                                <a
                                    href="{{ route($link['route']) }}"
                                    class="group relative flex items-center gap-3 px-3 py-2.5 transition {{ $active ? 'bg-white/10 text-white ring-1 ring-white/10' : 'text-white/55 hover:bg-white/5 hover:text-white' }}"
                                >
                                    <span class="absolute left-0 top-1/2 h-5 w-[3px] -translate-y-1/2 rounded-r-full bg-primary transition-all duration-200 {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-40' }}"></span>
                                    <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-xs transition {{ $active ? 'text-primary' : 'text-white/40 group-hover:text-white/70' }}"></i>
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>

            <div class="relative border-t border-white/10 p-4">
                <div class="flex items-center gap-3 bg-white/5 px-3 py-2.5 ring-1 ring-white/5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-shade text-xs font-bold uppercase text-white shadow-sm">
                        {{ Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->map(fn($p) => mb_substr($p, 0, 1))->take(2)->join('') }}
                    </span>
                    @php
                        $roleLabels = [
                            'client' => 'Client',
                            'gestionnaire' => 'Gestionnaire',
                            'admin' => 'Administrateur',
                            'super_admin' => 'Super admin',
                        ];
                        $roleValue = auth()->user()->role?->value ?? auth()->user()->role;
                    @endphp
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                        <span class="block truncate text-xs text-white/40">{{ $roleLabels[$roleValue] ?? $roleValue }}</span>
                    </span>
                </div>
                <a href="{{ route('home') }}" class="mt-1 flex items-center gap-3 px-3 py-2.5 text-xs text-white/60 transition hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-arrow-left w-4 text-center text-[10px]"></i>
                    Retour à la boutique
                </a>
            </div>
        </aside>

        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-secondary-shade/40 lg:hidden"
        ></div>

        <div class="min-w-0 flex-1">

            {{-- Topbar mobile --}}
            <div class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-secondary-shade/10 bg-white/90 px-6 backdrop-blur-sm dark:border-white/10 dark:bg-[#16201f]/90 lg:hidden">
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="text-secondary-shade dark:text-white/70">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <img src="{{ asset('images/Khalil_shop-cropped.svg') }}" alt="KhalilShop" class="h-8 w-auto">
                <div class="flex items-center gap-1">
                    <x-theme-toggle />
                    <x-notification-bell />
                </div>
            </div>

            {{-- Topbar desktop --}}
            <div class="sticky top-0 z-20 hidden h-16 items-center justify-between gap-2 border-b border-secondary-shade/10 bg-white/80 px-8 backdrop-blur-sm dark:border-white/10 dark:bg-[#16201f]/80 lg:flex">
                <p class="text-sm font-medium text-secondary-shade/70 dark:text-white/50">@yield('title', 'Administration')</p>
                <div class="flex items-center gap-2">
                    <x-theme-toggle />
                    <x-notification-bell />
                </div>
            </div>

            <main class="p-6 sm:p-10">
                <div class="mx-auto max-w-6xl">
                    @yield('content')
                </div>
            </main>
        </div>

    </div>

    {{-- Notifications flash (toasts) — auto-masquées après quelques secondes, fermables à tout moment --}}
    <div class="pointer-events-none fixed inset-x-4 top-4 z-[100] flex flex-col items-center gap-3 sm:inset-x-auto sm:right-4 sm:items-end" aria-live="polite">
        @if(session('status'))
            <div
                x-data="{ show: false }"
                x-init="setTimeout(() => show = true, 30); setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 sm:translate-x-4 sm:translate-y-0" x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 bg-white px-5 py-4 text-sm text-secondary-shade shadow-xl ring-1 ring-black/5 dark:bg-[#16201f] dark:text-white dark:ring-white/10"
            >
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-500/15 dark:text-green-400">
                    <i class="fa-solid fa-check text-[11px]"></i>
                </span>
                <span class="flex-1">{{ session('status') }}</span>
                <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-secondary-shade/40 transition hover:text-secondary-shade dark:text-white/40 dark:hover:text-white">
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
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 bg-white px-5 py-4 text-sm text-secondary-shade shadow-xl ring-1 ring-black/5 dark:bg-[#16201f] dark:text-white dark:ring-white/10"
            >
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400">
                    <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                </span>
                <ul class="flex-1 list-disc space-y-1 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-secondary-shade/40 transition hover:text-secondary-shade dark:text-white/40 dark:hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

</body>
</html>
