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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-grey-tint text-secondary-shade antialiased" x-data="{ sidebarOpen: false }">

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
                        ['route' => 'admin.promotions.index', 'group' => 'admin.promotions.*', 'icon' => 'fa-percent', 'label' => 'Promotions'],
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
            <div class="flex h-16 items-center justify-between border-b border-secondary-shade/10 bg-white px-6 lg:hidden">
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="text-secondary-shade">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <img src="{{ asset('images/Khalil_shop-cropped.svg') }}" alt="KhalilShop" class="h-8 w-auto">
                <span class="w-4"></span>
            </div>

            <main class="p-6 sm:p-10">
                <div class="mx-auto max-w-6xl">

                    @if(session('status'))
                        <div class="mb-6 border border-primary/30 bg-primary-tint px-5 py-4 text-sm text-primary-shade">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 border border-primary/30 bg-primary-tint px-5 py-4 text-sm text-primary-shade">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

    </div>

</body>
</html>
