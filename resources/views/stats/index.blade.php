@extends('layouts.app')

@section('title', 'Statistik & Analitik Tontonan — CineLog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="border-b-2 border-slate-800 pb-6">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/40 text-xs font-mono font-bold mb-2">
            <x-lucide-bar-chart-2 class="w-3.5 h-3.5" />
            <span>Personal Cinema Analytics</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">
            STATISTIK <span class="text-purple-400">TONTONAN</span>
        </h1>
        <p class="text-xs sm:text-sm text-zinc-400 font-mono mt-1">
            Ringkasan data tontonan, distribusi rating, dan genre terfavorit dari kurator.
        </p>
    </div>

    <!-- Main 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Metric: Total Reviews -->
        <div class="bg-[#161622] border-2 border-slate-700 p-5 rounded-2xl shadow-[4px_4px_0px_0px_#F59E0B] space-y-2">
            <div class="flex items-center justify-between text-amber-400">
                <span class="text-xs font-mono font-bold uppercase tracking-wider">Total Diulas</span>
                <x-lucide-film class="w-5 h-5" />
            </div>
            <div class="text-3xl sm:text-4xl font-black font-mono text-white">
                {{ $totalReviews }}
            </div>
            <span class="text-xs text-zinc-400 font-mono block">Judul film & serial</span>
        </div>

        <!-- Metric: Film vs Series -->
        <div class="bg-[#161622] border-2 border-slate-700 p-5 rounded-2xl shadow-[4px_4px_0px_0px_#06B6D4] space-y-2">
            <div class="flex items-center justify-between text-cyan-400">
                <span class="text-xs font-mono font-bold uppercase tracking-wider">Komposisi</span>
                <x-lucide-pie-chart class="w-5 h-5" />
            </div>
            <div class="text-xl sm:text-2xl font-black font-mono text-white pt-1">
                {{ $totalMovies }} <span class="text-xs text-zinc-400 font-normal">Film</span> / {{ $totalSeries }} <span class="text-xs text-zinc-400 font-normal">Series</span>
            </div>
            <span class="text-xs text-zinc-400 font-mono block">Distribusi format</span>
        </div>

        <!-- Metric: Avg Rating -->
        <div class="bg-[#161622] border-2 border-slate-700 p-5 rounded-2xl shadow-[4px_4px_0px_0px_#A855F7] space-y-2">
            <div class="flex items-center justify-between text-purple-400">
                <span class="text-xs font-mono font-bold uppercase tracking-wider">Rata-Rata Skor</span>
                <x-lucide-star class="w-5 h-5 fill-purple-400" />
            </div>
            <div class="text-3xl sm:text-4xl font-black font-mono text-white">
                {{ number_format($avgRating, 1) }} <span class="text-sm text-zinc-400">/ 10.0</span>
            </div>
            <span class="text-xs text-zinc-400 font-mono block">Skor bintang rata-rata</span>
        </div>

        <!-- Metric: Watch Time -->
        <div class="bg-[#161622] border-2 border-slate-700 p-5 rounded-2xl shadow-[4px_4px_0px_0px_#10B981] space-y-2">
            <div class="flex items-center justify-between text-emerald-400">
                <span class="text-xs font-mono font-bold uppercase tracking-wider">Estimasi Waktu</span>
                <x-lucide-clock class="w-5 h-5" />
            </div>
            <div class="text-3xl sm:text-4xl font-black font-mono text-white">
                {{ $totalRuntimeHours }} <span class="text-sm text-zinc-400">Jam</span>
            </div>
            <span class="text-xs text-zinc-400 font-mono block">Total durasi tontonan</span>
        </div>
    </div>

    <!-- 2 Column Section: Rating Distribution & Top Genres -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Rating Distribution Bars -->
        <div class="bg-[#161622] border-2 border-slate-700 p-6 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <h2 class="text-sm font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2 border-b border-slate-800 pb-3">
                <x-lucide-bar-chart class="w-4 h-4" />
                <span>Distribusi Penilaian Bintang</span>
            </h2>

            <div class="space-y-3 font-mono text-xs">
                @foreach($ratingDistribution as $score => $count)
                    @php 
                        $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-14 font-bold text-amber-400 text-right">{{ $score }} ★</span>
                        <div class="flex-1 h-4 bg-zinc-900 border border-slate-700 rounded overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-yellow-300 transition-all duration-500 rounded" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="w-8 text-zinc-400 font-bold text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Genres List -->
        <div class="bg-[#161622] border-2 border-slate-700 p-6 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <h2 class="text-sm font-mono font-bold uppercase tracking-wider text-purple-400 flex items-center gap-2 border-b border-slate-800 pb-3">
                <x-lucide-tags class="w-4 h-4" />
                <span>Genre Terbanyak Ditonton</span>
            </h2>

            <div class="space-y-3 font-mono text-xs">
                @foreach($topGenres as $genre)
                    @php 
                        $percentage = $totalReviews > 0 ? ($genre->movies_series_count / $totalReviews) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-24 font-bold text-zinc-200 truncate">{{ $genre->name }}</span>
                        <div class="flex-1 h-4 bg-zinc-900 border border-slate-700 rounded overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-400 transition-all duration-500 rounded" 
                                 style="width: {{ min(100, $percentage * 1.5) }}%"></div>
                        </div>
                        <span class="w-10 text-purple-400 font-bold text-right">{{ $genre->movies_series_count }} jdl</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Directors -->
    @if($topDirectors->isNotEmpty())
        <div class="bg-[#14141E] border-2 border-slate-800 rounded-2xl p-6 space-y-4">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2">
                <x-lucide-clapperboard class="w-4 h-4" />
                <span>Sutradara Paling Sering Diulas</span>
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                @foreach($topDirectors as $dir)
                    <div class="bg-[#181826] border border-slate-700 p-3 rounded-xl shadow-[2px_2px_0px_#000] text-center font-mono">
                        <div class="w-8 h-8 rounded-full bg-cyan-500/20 text-cyan-400 border border-cyan-500/40 flex items-center justify-center mx-auto mb-2">
                            <x-lucide-user class="w-4 h-4" />
                        </div>
                        <h4 class="font-bold text-white text-xs truncate" title="{{ $dir->director }}">
                            {{ $dir->director }}
                        </h4>
                        <span class="text-[11px] text-zinc-400">
                            {{ $dir->total_count }} judul
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
