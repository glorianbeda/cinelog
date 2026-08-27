<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') — CineLog Management</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-[#0D0D12] text-zinc-100 selection:bg-purple-500 selection:text-white antialiased">
    <!-- Admin Top Header -->
    <header class="sticky top-0 z-40 bg-[#12121A] border-b-2 border-slate-700 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Branding & Status -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded bg-purple-500 flex items-center justify-center border-2 border-black shadow-[2px_2px_0px_#fff]">
                            <x-lucide-shield class="w-4 h-4 text-black" />
                        </div>
                        <span class="font-black text-lg font-mono text-white tracking-tight">
                            CINELOG<span class="text-purple-400">.ADMIN</span>
                        </span>
                    </a>

                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[11px] font-mono font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Solo Admin Active
                    </span>
                </div>

                <!-- Navigation Tabs -->
                <nav class="hidden md:flex items-center gap-1 font-mono text-xs font-bold">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-purple-500 text-black border-2 border-black shadow-[2px_2px_0px_#fff]' : 'text-zinc-400 hover:text-white hover:bg-zinc-800' }}">
                        <x-lucide-layout-dashboard class="w-4 h-4" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-all {{ request()->routeIs('admin.reviews.*') ? 'bg-purple-500 text-black border-2 border-black shadow-[2px_2px_0px_#fff]' : 'text-zinc-400 hover:text-white hover:bg-zinc-800' }}">
                        <x-lucide-film class="w-4 h-4" />
                        <span>Ulasan Film</span>
                    </a>
                    <a href="{{ route('admin.watchlist.index') }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-all {{ request()->routeIs('admin.watchlist.*') ? 'bg-purple-500 text-black border-2 border-black shadow-[2px_2px_0px_#fff]' : 'text-zinc-400 hover:text-white hover:bg-zinc-800' }}">
                        <x-lucide-bookmark class="w-4 h-4" />
                        <span>Watchlist</span>
                        @if(($pendingReviewsCount ?? 0) > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 min-w-[18px] text-[10px] font-mono font-black rounded-full shadow-[1px_1px_0px_#000] {{ request()->routeIs('admin.watchlist.*') ? 'bg-black text-amber-300 ring-1 ring-black' : 'bg-amber-400 text-black ring-2 ring-amber-400/50 animate-pulse' }}" title="{{ $pendingReviewsCount }} tontonan selesai siap diberi rating">
                                {{ $pendingReviewsCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-purple-500 text-black border-2 border-black shadow-[2px_2px_0px_#fff]' : 'text-zinc-400 hover:text-white hover:bg-zinc-800' }}">
                        <x-lucide-settings class="w-4 h-4" />
                        <span>Pengaturan</span>
                    </a>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <!-- Quick Add Review Button -->
                    <a href="{{ route('admin.reviews.create') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-mono font-bold bg-amber-400 hover:bg-amber-300 text-black border-2 border-black shadow-[2px_2px_0px_#fff] transition-all">
                        <x-lucide-plus class="w-4 h-4" />
                        <span class="hidden sm:inline">Tulis Ulasan</span>
                    </a>

                    <!-- View Public Site Button -->
                    <a href="{{ route('home') }}" 
                       target="_blank"
                       title="Lihat Tampilan Publik"
                       class="p-2 text-zinc-400 hover:text-cyan-400 hover:bg-zinc-800 rounded-lg border border-zinc-700 transition-colors">
                        <x-lucide-external-link class="w-4 h-4" />
                    </a>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                title="Keluar dari panel"
                                class="p-2 text-rose-400 hover:text-white hover:bg-rose-600/30 rounded-lg border border-rose-900/50 transition-colors">
                            <x-lucide-log-out class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Admin Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-flash-messages />
        @yield('content')
    </main>

    <!-- Admin Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs font-mono text-zinc-500">
        CineLog Solo Management Panel • Ditenagai Laravel & TMDB Open API
    </footer>
</body>
</html>
