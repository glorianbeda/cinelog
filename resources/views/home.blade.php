@extends('layouts.app')

@section('title', ($owner ? $owner->name . ' — ' : '') . 'Jurnal Ulasan & Rating Film')

@section('content')
<div class="space-y-16 py-8">
    <!-- HERO SECTION: Owner Persona & Presentation -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-[#181824] via-[#14141E] to-[#0E0E14] border-2 border-slate-700 rounded-3xl p-6 sm:p-10 lg:p-12 shadow-[8px_8px_0px_0px_#A855F7]">
            <!-- Subtle Grid Background -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8 lg:gap-12 justify-between">
                <!-- Left: Owner Intro & Tagline -->
                <div class="space-y-6 max-w-2xl text-center lg:text-left">
                    <!-- Owner Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-400/10 border-2 border-amber-400 text-amber-400 rounded-full text-xs font-mono font-bold shadow-[2px_2px_0px_0px_#F59E0B]">
                        <x-lucide-sparkles class="w-3.5 h-3.5" />
                        <span>Curated Cinema Diary</span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-mono tracking-tight text-white leading-none">
                            {{ $owner->name ?? 'Cinema Vault' }}
                        </h1>
                        <p class="text-base sm:text-lg text-zinc-300 font-medium leading-relaxed">
                            {{ $owner->bio ?? 'Jurnal & portofolio ulasan film dan series pilihan dengan penilaian mendalam dan rating beranimasi.' }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2 font-mono">
                        <a href="{{ route('catalog.index') }}" 
                           class="neo-btn px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-sm font-bold shadow-[4px_4px_0px_0px_#fff]">
                            <x-lucide-film class="w-4 h-4 mr-2" />
                            <span>Jelajahi Semua Ulasan</span>
                        </a>

                        <a href="{{ route('watchlist.public') }}" 
                           class="neo-btn px-5 py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-bold border-2 border-slate-600 shadow-[4px_4px_0px_0px_#06B6D4]">
                            <x-lucide-bookmark class="w-4 h-4 mr-2 text-cyan-400" />
                            <span>Watchlist Antrean</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Stats Showcase Box -->
                <div class="w-full lg:w-auto grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 gap-4 shrink-0">
                    <!-- Stat Card: Total Reviewed -->
                    <div class="bg-[#12121A] border-2 border-slate-700 p-4 rounded-xl shadow-[4px_4px_0px_0px_#F59E0B]">
                        <div class="flex items-center gap-2 text-amber-400 mb-1">
                            <x-lucide-clapperboard class="w-4 h-4" />
                            <span class="text-xs font-mono font-bold uppercase">Total Ulasan</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black font-mono text-white">
                            {{ $totalReviews }}
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">Film & Serial</span>
                    </div>

                    <!-- Stat Card: Avg Rating -->
                    <div class="bg-[#12121A] border-2 border-slate-700 p-4 rounded-xl shadow-[4px_4px_0px_0px_#A855F7]">
                        <div class="flex items-center gap-2 text-purple-400 mb-1">
                            <x-lucide-star class="w-4 h-4 fill-purple-400" />
                            <span class="text-xs font-mono font-bold uppercase">Rata-Rata</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black font-mono text-white">
                            {{ number_format($avgRating, 1) }} <span class="text-sm text-zinc-400">/ 5.0</span>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">Skor Personal</span>
                    </div>

                    <!-- Stat Card: Watch Time -->
                    <div class="bg-[#12121A] border-2 border-slate-700 p-4 rounded-xl shadow-[4px_4px_0px_0px_#06B6D4] col-span-2 sm:col-span-1 lg:col-span-2">
                        <div class="flex items-center gap-2 text-cyan-400 mb-1">
                            <x-lucide-clock class="w-4 h-4" />
                            <span class="text-xs font-mono font-bold uppercase">Jam Menonton</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black font-mono text-white">
                            {{ $totalRuntimeHours }} <span class="text-sm text-zinc-400">Jam</span>
                        </div>
                        <span class="text-[11px] text-zinc-400 font-mono">Total durasi tontonan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: Masterpieces Showcase (Rating >= 4.5 Stars) -->
    @if($featuredMasterpieces->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-yellow-400/15 text-yellow-400 border border-yellow-400/40">
                        <x-lucide-trophy class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black font-mono tracking-tight text-white">
                            MASTERPIECES <span class="text-amber-400">SELECTION</span>
                        </h2>
                        <p class="text-xs text-zinc-400 font-mono">
                            Film & Series dengan nilai tertinggi (4.5 - 5.0 ★)
                        </p>
                    </div>
                </div>

                <a href="{{ route('catalog.index', ['min_rating' => 4.5]) }}" 
                   class="hidden sm:inline-flex items-center gap-1 text-xs font-mono font-bold text-amber-400 hover:text-amber-300">
                    <span>Lihat Semua Masterpiece</span>
                    <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($featuredMasterpieces as $review)
                    <x-movie-card :review="$review" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- SECTION: Latest Reviews Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-purple-500/15 text-purple-400 border border-purple-500/40">
                    <x-lucide-clock-3 class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black font-mono tracking-tight text-white">
                        ULASAN <span class="text-purple-400">TERBARU</span>
                    </h2>
                    <p class="text-xs text-zinc-400 font-mono">
                        Catatan tontonan yang baru saja selesai diulas
                    </p>
                </div>
            </div>

            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-xs font-mono font-bold text-zinc-200 border border-zinc-700 transition-colors">
                <span>Buka Katalog Lengkap</span>
                <x-lucide-chevron-right class="w-4 h-4" />
            </a>
        </div>

        @if($latestReviews->isEmpty())
            <div class="p-12 text-center bg-[#14141E] border-2 border-dashed border-slate-700 rounded-2xl space-y-3">
                <x-lucide-film class="w-10 h-10 mx-auto text-zinc-600" />
                <h3 class="font-bold text-zinc-300 font-mono">Belum ada ulasan yang dipublikasikan</h3>
                <p class="text-xs text-zinc-500 font-mono">Masuk ke panel admin untuk menambahkan film dan ulasan pertama Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($latestReviews as $review)
                    <x-movie-card :review="$review" />
                @endforeach
            </div>
        @endif
    </section>

    <!-- SECTION: Currently Watching Spotlight -->
    @if($currentlyWatching->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between border-b-2 border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-emerald-500/15 text-emerald-400 border border-emerald-500/40">
                        <x-lucide-play-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black font-mono tracking-tight text-white">
                            SEDANG <span class="text-emerald-400">DITONTON</span>
                        </h2>
                        <p class="text-xs text-zinc-400 font-mono">
                            Serial & tontonan yang sedang berjalan
                        </p>
                    </div>
                </div>

                <a href="{{ route('watchlist.public', ['status' => 'watching']) }}" 
                   class="text-xs font-mono font-bold text-emerald-400 hover:text-emerald-300">
                    Lihat Watchlist →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($currentlyWatching as $item)
                    <div class="flex gap-3 bg-[#161622] border-2 border-slate-700 p-3 rounded-xl shadow-[4px_4px_0px_0px_#10B981]">
                        <img src="{{ $item->movieSeries->poster_image_url }}" 
                             alt="{{ $item->movieSeries->title }}"
                             class="w-16 h-24 object-cover rounded-lg border border-slate-700 shrink-0">
                        
                        <div class="flex flex-col justify-between flex-1 min-w-0">
                            <div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-500/30">
                                    {{ $item->movieSeries->type === 'movie' ? 'Film' : 'Series' }}
                                </span>
                                <h4 class="font-bold text-sm text-white truncate mt-1">
                                    {{ $item->movieSeries->title }}
                                </h4>
                            </div>

                            <div class="text-xs font-mono text-zinc-400">
                                @if($item->movieSeries->type !== 'movie')
                                    <span class="text-emerald-400 font-bold">
                                        S{{ $item->current_season }} Ep {{ $item->current_episode }}
                                    </span>
                                    @if($item->movieSeries->total_episodes)
                                        / {{ $item->movieSeries->total_episodes }}
                                    @endif
                                @else
                                    <span>Plan to Watch</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- SECTION: Explore by Genre Pills -->
    @if($genres->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#12121A] border-2 border-slate-800 rounded-2xl p-6 sm:p-8 space-y-4">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 flex items-center gap-2">
                    <x-lucide-tag class="w-4 h-4 text-purple-400" />
                    <span>Jelajahi Berdasarkan Genre Populer</span>
                </h3>

                <div class="flex flex-wrap gap-2.5">
                    @foreach($genres as $genre)
                        <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-zinc-900 hover:bg-purple-500/20 text-zinc-300 hover:text-purple-300 border-2 border-slate-700 hover:border-purple-500 text-xs font-mono font-bold transition-all shadow-[2px_2px_0px_#000] hover:shadow-[3px_3px_0px_#A855F7]">
                            <span>{{ $genre->name }}</span>
                            <span class="px-1.5 py-0.2 bg-zinc-800 text-zinc-400 rounded text-[10px]">
                                {{ $genre->movies_series_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
