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
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col bg-secondary-shade text-white transition-transform lg:static lg:translate-x-0"
        >
            <div class="flex h-20 items-center px-6">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/Khalil_shop-cropped.svg') }}" alt="KhalilShop" class="h-9 w-auto">
                </a>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-4 text-sm">
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'group' => 'admin.dashboard', 'icon' => 'fa-gauge', 'label' => 'Tableau de bord'],
                        ['route' => 'admin.products.index', 'group' => 'admin.products.*', 'icon' => 'fa-shirt', 'label' => 'Produits'],
                        ['route' => 'admin.categories.index', 'group' => 'admin.categories.*', 'icon' => 'fa-sitemap', 'label' => 'Catégories'],
                        ['route' => 'admin.orders.index', 'group' => 'admin.orders.*', 'icon' => 'fa-bag-shopping', 'label' => 'Commandes'],
                        ['route' => 'admin.coupons.index', 'group' => 'admin.coupons.*', 'icon' => 'fa-tag', 'label' => 'Codes promo'],
                        ['route' => 'admin.reviews.index', 'group' => 'admin.reviews.*', 'icon' => 'fa-star', 'label' => 'Avis clients'],
                        ['route' => 'admin.returns.index', 'group' => 'admin.returns.*', 'icon' => 'fa-rotate-left', 'label' => 'Retours'],
                        ['route' => 'admin.banners.index', 'group' => 'admin.banners.*', 'icon' => 'fa-image', 'label' => 'Accueil / Bannières'],
                    ];
                @endphp

                @foreach($links as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition {{ request()->routeIs($link['group']) ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                    >
                        <i class="fa-solid {{ $link['icon'] }} w-4 text-center text-xs"></i>
                        {{ $link['label'] }}
                    </a>
                @endforeach

                @can('create', [App\Models\User::class, App\Enums\Role::Gestionnaire])
                    <a
                        href="{{ route('admin.staff.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition {{ request()->routeIs('admin.staff.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-users w-4 text-center text-xs"></i>
                        Équipe
                    </a>
                @endcan

                @can('viewAny', App\Models\Campaign::class)
                    <a
                        href="{{ route('admin.campaigns.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition {{ request()->routeIs('admin.campaigns.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-envelope-open-text w-4 text-center text-xs"></i>
                        Campagnes email
                    </a>
                @endcan

                @can('viewAny', App\Models\Setting::class)
                    <a
                        href="{{ route('admin.settings.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}"
                    >
                        <i class="fa-solid fa-gear w-4 text-center text-xs"></i>
                        Configuration
                    </a>
                @endcan
            </nav>

            <div class="border-t border-white/10 p-4">
                <p class="px-3 text-xs text-white/40">Connecté en tant que</p>
                <p class="px-3 text-sm font-medium">{{ auth()->user()->name }}</p>
                <a href="{{ route('home') }}" class="mt-3 flex items-center gap-2 px-3 py-2 text-xs text-white/60 transition hover:text-white">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    Retour à la boutique
                </a>
            </div>
        </aside>

        <div class="min-w-0 flex-1">

            {{-- Topbar mobile --}}
            <div class="flex h-16 items-center justify-between border-b border-secondary-shade/10 bg-white px-6 dark:border-white/10 dark:bg-[#16201f] lg:hidden">
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
            <div class="hidden h-16 items-center justify-end gap-2 border-b border-secondary-shade/10 bg-white px-8 dark:border-white/10 dark:bg-[#16201f] lg:flex">
                <x-theme-toggle />
                <x-notification-bell />
            </div>

            <main class="p-6 sm:p-10">
                <div class="mx-auto max-w-6xl">

                    @if(session('status'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start justify-between gap-4 border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
                            <span>{{ session('status') }}</span>
                            <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-green-700/60 transition hover:text-green-700 dark:text-green-400/60 dark:hover:text-green-400">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start justify-between gap-4 border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" @click="show = false" aria-label="Fermer" class="shrink-0 text-red-700/60 transition hover:text-red-700 dark:text-red-400/60 dark:hover:text-red-400">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

    </div>

</body>
</html>
