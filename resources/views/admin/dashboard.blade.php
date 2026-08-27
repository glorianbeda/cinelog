@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Top Welcome & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#161622] border-2 border-slate-700 p-6 rounded-2xl shadow-[6px_6px_0px_0px_#A855F7]">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-white flex items-center gap-2">
                <span>Halo, {{ $owner->name }}!</span>
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 font-mono">
                Panel manajemen ulasan sinematik & antrean tontonan Anda.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.reviews.create') }}" 
               class="neo-btn px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-xs font-mono font-bold shadow-[3px_3px_0px_0px_#fff]">
                <x-lucide-plus class="w-4 h-4 mr-1.5" />
                <span>Tambah Ulasan Film</span>
            </a>

            <a href="{{ route('admin.watchlist.create') }}" 
               class="neo-btn px-4 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black text-xs font-mono font-bold shadow-[3px_3px_0px_0px_#fff]">
                <x-lucide-bookmark class="w-4 h-4 mr-1.5" />
                <span>Tambah Watchlist</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-[#161622] border-2 border-slate-700 p-4 sm:p-5 rounded-xl shadow-[4px_4px_0px_0px_#F59E0B]">
            <div class="flex items-center justify-between text-amber-400 mb-1">
                <span class="text-xs font-mono font-bold uppercase">Total Ulasan</span>
                <x-lucide-film class="w-4 h-4" />
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-white">{{ $totalReviews }}</div>
            <span class="text-[11px] text-zinc-400 font-mono">{{ $totalMovies }} Film • {{ $totalSeries }} Series</span>
        </div>

        <div class="bg-[#161622] border-2 border-slate-700 p-4 sm:p-5 rounded-xl shadow-[4px_4px_0px_0px_#A855F7]">
            <div class="flex items-center justify-between text-purple-400 mb-1">
                <span class="text-xs font-mono font-bold uppercase">Rata-Rata Skor</span>
                <x-lucide-star class="w-4 h-4 fill-purple-400" />
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-white">{{ number_format($avgRating, 1) }} ★</div>
            <span class="text-[11px] text-zinc-400 font-mono">Skala penilaian 5.0</span>
        </div>

        <div class="bg-[#161622] border-2 border-slate-700 p-4 sm:p-5 rounded-xl shadow-[4px_4px_0px_0px_#06B6D4]">
            <div class="flex items-center justify-between text-cyan-400 mb-1">
                <span class="text-xs font-mono font-bold uppercase">Antrean Aktif</span>
                <x-lucide-bookmark class="w-4 h-4" />
            </div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-white">{{ $totalWatchlist }}</div>
            <span class="text-[11px] text-zinc-400 font-mono">Dalam daftar tontonan</span>
        </div>

        <div class="bg-[#161622] border-2 border-slate-700 p-4 sm:p-5 rounded-xl shadow-[4px_4px_0px_0px_#10B981]">
            <div class="flex items-center justify-between text-emerald-400 mb-1">
                <span class="text-xs font-mono font-bold uppercase">Status TMDB API</span>
                <x-lucide-database class="w-4 h-4" />
            </div>
            <div class="text-sm font-bold font-mono text-emerald-400 pt-1">
                {{ !empty($owner->tmdb_api_key) ? 'Terhubung (Active)' : 'Belum Ada Key' }}
            </div>
            <a href="{{ route('admin.settings.index') }}" class="text-[11px] text-zinc-400 hover:text-white underline font-mono">
                Kelola Kunci API
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Chart -->
        <div class="lg:col-span-2 bg-[#161622] border-2 border-slate-700 p-5 sm:p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                <x-lucide-activity class="w-4 h-4" />
                <span>Tren Menonton Bulanan ({{ date('Y') }})</span>
            </h3>
            <div class="h-64 relative">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Genre Breakdown Chart -->
        <div class="bg-[#161622] border-2 border-slate-700 p-5 sm:p-6 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-purple-400 flex items-center gap-2">
                <x-lucide-pie-chart class="w-4 h-4" />
                <span>Distribusi Genre Teratas</span>
            </h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="genreChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Grid: Recent Reviews & Active Watchlist -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Reviews Table -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <x-lucide-film class="w-4 h-4 text-amber-400" />
                    <span>Ulasan Terakhir Ditambahkan</span>
                </h3>
                <a href="{{ route('admin.reviews.index') }}" class="text-xs font-mono text-amber-400 hover:underline">
                    Lihat Semua →
                </a>
            </div>

            @if($recentReviews->isEmpty())
                <p class="text-xs text-zinc-500 font-mono py-4 text-center">Belum ada ulasan.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentReviews as $rev)
                        <div class="flex items-center justify-between gap-3 p-2.5 bg-zinc-900/60 border border-slate-800 rounded-xl">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $rev->movieSeries->poster_image_url }}" alt="" class="w-10 h-14 object-cover rounded-lg border border-slate-700 shrink-0">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-white text-xs truncate">{{ $rev->movieSeries->title }}</h4>
                                    <div class="flex items-center gap-2 text-[10px] font-mono text-zinc-400 mt-0.5">
                                        <span class="text-amber-400 font-bold">{{ number_format($rev->rating_overall, 1) }} ★</span>
                                        <span>•</span>
                                        <span>{{ $rev->watched_date?->format('d M Y') ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="{{ route('admin.reviews.edit', $rev) }}" class="p-1.5 text-zinc-400 hover:text-purple-400 bg-zinc-800 rounded">
                                    <x-lucide-edit class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('reviews.show', $rev->movieSeries->slug) }}" target="_blank" class="p-1.5 text-zinc-400 hover:text-cyan-400 bg-zinc-800 rounded">
                                    <x-lucide-external-link class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Active Watchlist Table -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <x-lucide-play-circle class="w-4 h-4 text-emerald-400" />
                    <span>Sedang Ditonton (Progress)</span>
                </h3>
                <a href="{{ route('admin.watchlist.index') }}" class="text-xs font-mono text-emerald-400 hover:underline">
                    Kelola Watchlist →
                </a>
            </div>

            @if($activeWatchlist->isEmpty())
                <p class="text-xs text-zinc-500 font-mono py-4 text-center">Tidak ada serial yang sedang aktif ditonton.</p>
            @else
                <div class="space-y-3">
                    @foreach($activeWatchlist as $item)
                        <div class="flex items-center justify-between gap-3 p-2.5 bg-zinc-900/60 border border-slate-800 rounded-xl">
                            <div class="min-w-0">
                                <h4 class="font-bold text-white text-xs truncate">{{ $item->movieSeries->title }}</h4>
                                <span class="text-xs font-mono text-emerald-400 font-bold">
                                    Season {{ $item->current_season }} Episode {{ $item->current_episode }}
                                </span>
                            </div>

                            <!-- Episode Quick Counter Actions -->
                            <div class="flex items-center gap-1 shrink-0 font-mono">
                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="w-7 h-7 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded font-bold text-xs flex items-center justify-center border border-slate-700">
                                        -
                                    </button>
                                </form>

                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="w-7 h-7 bg-emerald-500 hover:bg-emerald-400 text-black rounded font-bold text-xs flex items-center justify-center border border-black shadow-[1px_1px_0px_#fff]">
                                        +
                                    </button>
                                </form>

                                <a href="{{ route('admin.reviews.create', ['watchlist_id' => $item->id]) }}" 
                                   title="Tandai selesai & buat ulasan"
                                   class="p-1.5 text-amber-400 hover:bg-amber-400 hover:text-black bg-zinc-800 rounded ml-1 border border-amber-500/40 transition-colors">
                                    <x-lucide-check class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js Setup Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart !== 'undefined') {
            // 1. Monthly Trend Bar Chart
            const ctxMonthly = document.getElementById('monthlyChart');
            if (ctxMonthly) {
                new Chart(ctxMonthly, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Jumlah Diulas',
                            data: {!! json_encode($chartData) !!},
                            backgroundColor: '#A855F7',
                            borderColor: '#C084FC',
                            borderWidth: 2,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { color: '#222230' },
                                ticks: { color: '#9CA3AF', font: { family: 'monospace' } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#222230' },
                                ticks: { color: '#9CA3AF', stepSize: 1, font: { family: 'monospace' } }
                            }
                        }
                    }
                });
            }

            // 2. Genre Doughnut Chart
            const ctxGenre = document.getElementById('genreChart');
            if (ctxGenre) {
                new Chart(ctxGenre, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($genreLabels) !!},
                        datasets: [{
                            data: {!! json_encode($genreData) !!},
                            backgroundColor: ['#F59E0B', '#A855F7', '#06B6D4', '#10B981', '#F43F5E', '#8B5CF6'],
                            borderColor: '#161622',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#D1D5DB', font: { family: 'monospace', size: 10 } }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endsection
