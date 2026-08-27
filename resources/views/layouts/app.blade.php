<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CineLog') — Jurnal Ulasan & Rating Film/Series</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-[#0D0D12] text-zinc-100 selection:bg-purple-500 selection:text-white antialiased">
    <!-- Top Decorative Line -->
    <div class="h-1.5 w-full bg-gradient-to-r from-amber-400 via-purple-500 to-cyan-400"></div>

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 bg-[#12121A]/95 backdrop-blur-md border-b-2 border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo & Owner Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-lg bg-amber-400 flex items-center justify-center border-2 border-white/20 shadow-[3px_3px_0px_0px_#A855F7] group-hover:-translate-y-0.5 group-hover:shadow-[4px_4px_0px_0px_#A855F7] transition-all">
                            <x-lucide-film class="w-5 h-5 text-black" />
                        </div>
                        <div>
                            <span class="font-black text-xl tracking-tight text-white flex items-center gap-1.5 font-mono">
                                CINE<span class="text-amber-400">LOG</span>
                            </span>
                            @if(isset($owner) && $owner)
                                <span class="block text-[11px] font-mono text-zinc-400 -mt-1 group-hover:text-purple-300 transition-colors">
                                    by {{ $owner->name }}
                                </span>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1 font-mono text-sm font-bold">
                    <a href="{{ route('home') }}" 
                       class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('home') ? 'text-amber-400 bg-zinc-800/80 border border-amber-400/30' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/50' }}">
                        Beranda
                    </a>
                    <a href="{{ route('catalog.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('catalog.*') ? 'text-amber-400 bg-zinc-800/80 border border-amber-400/30' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/50' }}">
                        Semua Ulasan
                    </a>
                    <a href="{{ route('watchlist.public') }}" 
                       class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('watchlist.*') ? 'text-amber-400 bg-zinc-800/80 border border-amber-400/30' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/50' }}">
                        Watchlist
                    </a>
                    <a href="{{ route('stats.index') }}" 
                       class="px-3.5 py-2 rounded-lg transition-all {{ request()->routeIs('stats.*') ? 'text-amber-400 bg-zinc-800/80 border border-amber-400/30' : 'text-zinc-300 hover:text-white hover:bg-zinc-800/50' }}">
                        Statistik
                    </a>
                </nav>

                <!-- Search & Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Quick Search Link -->
                    <a href="{{ route('catalog.index') }}" 
                       title="Cari ulasan"
                       class="p-2 text-zinc-400 hover:text-amber-400 hover:bg-zinc-800 rounded-lg border border-transparent hover:border-zinc-700 transition-all">
                        <x-lucide-search class="w-5 h-5" />
                    </a>

                    @auth
                        <!-- Admin Management Link (Only visible when already authenticated) -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-mono font-bold bg-purple-500 hover:bg-purple-400 text-black border-2 border-black shadow-[2px_2px_0px_#fff] transition-all">
                            <x-lucide-shield-check class="w-4 h-4" />
                            <span>Panel Admin</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t-2 border-slate-800 bg-[#0A0A0E] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Branding & Bio -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-amber-400 flex items-center justify-center border border-black shadow-[2px_2px_0px_#A855F7]">
                        <x-lucide-film class="w-4 h-4 text-black" />
                    </div>
                    <div>
                        <span class="font-mono font-bold text-white text-base">CineLog</span>
                        @if(isset($owner) && $owner)
                            <p class="text-xs text-zinc-400">
                                Jurnal & Portofolio Ulasan Film oleh <span class="text-amber-400 font-semibold">{{ $owner->name }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Navigation Quick Links -->
                <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-zinc-400">
                    <a href="{{ route('home') }}" class="hover:text-amber-400 transition-colors">Beranda</a>
                    <span class="text-zinc-700">•</span>
                    <a href="{{ route('catalog.index') }}" class="hover:text-amber-400 transition-colors">Katalog</a>
                    <span class="text-zinc-700">•</span>
                    <a href="{{ route('watchlist.public') }}" class="hover:text-amber-400 transition-colors">Watchlist</a>
                    <span class="text-zinc-700">•</span>
                    <a href="{{ route('stats.index') }}" class="hover:text-amber-400 transition-colors">Statistik</a>
                </div>

                <!-- Open Source / TMDB Attribution -->
                <div class="flex items-center gap-2 text-xs font-mono text-zinc-500">
                    <x-lucide-database class="w-3.5 h-3.5 text-cyan-400" />
                    <span>Data didukung oleh TMDB Open API</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
