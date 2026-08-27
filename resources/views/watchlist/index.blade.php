@extends('layouts.app')

@section('title', 'Watchlist & Antrean Tontonan — CineLog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b-2 border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-mono font-bold mb-2">
                <x-lucide-bookmark class="w-3.5 h-3.5" />
                <span>Pelacak Tontonan</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">
                WATCHLIST <span class="text-cyan-400">ANTREAN</span>
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 font-mono mt-1">
                Daftar film dan serial yang sedang diikuti atau masuk dalam rencana tontonan kurator.
            </p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
        <a href="{{ route('watchlist.public') }}" 
           class="px-3.5 py-2 rounded-lg border-2 font-bold transition-all
           {{ $status === 'all' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Semua ({{ $counts['all'] }})
        </a>

        <a href="{{ route('watchlist.public', ['status' => 'watching']) }}" 
           class="px-3.5 py-2 rounded-lg border-2 font-bold transition-all
           {{ $status === 'watching' ? 'bg-emerald-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Sedang Menonton ({{ $counts['watching'] }})
        </a>

        <a href="{{ route('watchlist.public', ['status' => 'plan_to_watch']) }}" 
           class="px-3.5 py-2 rounded-lg border-2 font-bold transition-all
           {{ $status === 'plan_to_watch' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Rencana Nonton ({{ $counts['plan_to_watch'] }})
        </a>

        <a href="{{ route('watchlist.public', ['status' => 'completed']) }}" 
           class="px-3.5 py-2 rounded-lg border-2 font-bold transition-all
           {{ $status === 'completed' ? 'bg-purple-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Selesai Ditonton ({{ $counts['completed'] }})
        </a>
    </div>

    <!-- Watchlist Grid -->
    @if($items->isEmpty())
        <div class="p-16 text-center bg-[#14141E] border-2 border-dashed border-slate-700 rounded-2xl space-y-3">
            <x-lucide-bookmark-x class="w-10 h-10 mx-auto text-zinc-600" />
            <h3 class="font-bold text-zinc-300 font-mono">Belum ada item dalam daftar ini</h3>
            <p class="text-xs text-zinc-500 font-mono">Daftar tontonan akan diperbarui secara berkala oleh pemilik.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($items as $item)
                @php 
                    $movie = $item->movieSeries;
                    $badge = $item->status_badge;
                @endphp
                <div class="flex gap-4 bg-[#161622] border-2 border-slate-700 p-4 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] hover:shadow-[6px_6px_0px_0px_#06B6D4] transition-all">
                    <!-- Poster Thumbnail -->
                    <img src="{{ $movie->poster_image_url }}" 
                         alt="{{ $movie->title }}" 
                         class="w-20 sm:w-24 aspect-[2/3] object-cover rounded-xl border border-slate-700 shrink-0">

                    <!-- Info Details -->
                    <div class="flex flex-col justify-between flex-1 min-w-0 font-mono text-xs">
                        <div class="space-y-1.5">
                            <!-- Status Badge -->
                            <div class="flex items-center justify-between gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>

                                @if($item->priority === 'high')
                                    <span class="px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[10px] font-bold">
                                        Prioritas Tinggi
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="font-bold text-white text-sm truncate" title="{{ $movie->title }}">
                                {{ $movie->title }}
                            </h3>

                            <span class="text-zinc-400 text-[11px] block">
                                {{ $movie->type === 'movie' ? 'Film' : 'Series' }} • {{ $movie->release_year ?? '-' }}
                            </span>
                        </div>

                        <!-- Progress or Notes -->
                        <div class="pt-2 border-t border-slate-800 space-y-2">
                            @if($movie->type !== 'movie')
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <div class="flex items-center gap-1.5 {{ $item->is_finished ? 'text-purple-300' : 'text-emerald-400' }} font-bold">
                                            @if($item->is_finished)
                                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-400" />
                                                <span>Selesai ({{ $item->current_episode }} eps)</span>
                                            @else
                                                <x-lucide-play class="w-3.5 h-3.5 text-emerald-400" />
                                                <span>S{{ $item->current_season }} Ep {{ $item->current_episode }}</span>
                                                @if($movie->total_episodes)
                                                    <span class="text-zinc-500 font-normal">/ {{ $movie->total_episodes }}</span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="text-[10px] font-mono text-zinc-400">{{ $item->progress_percentage }}%</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-zinc-800 rounded-full h-1.5 border border-slate-700 overflow-hidden">
                                        <div class="h-full rounded-full {{ $item->is_finished ? 'bg-gradient-to-r from-purple-400 to-emerald-400' : 'bg-gradient-to-r from-cyan-400 to-emerald-400' }}" 
                                             style="width: {{ $item->progress_percentage }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($item->watch_platform)
                                <div class="text-[11px] text-zinc-400">
                                    Platform: <span class="text-zinc-200 font-bold">{{ $item->watch_platform }}</span>
                                </div>
                            @endif

                            @if($movie->reviews->isNotEmpty())
                                @php $review = $movie->reviews->first(); @endphp
                                <a href="{{ route('reviews.show', $movie->slug) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-400/10 hover:bg-amber-400/20 text-amber-300 border border-amber-400/30 rounded-lg text-[11px] font-bold mt-1 transition-colors">
                                    <x-lucide-star class="w-3 h-3 fill-amber-400 text-amber-400" />
                                    <span>Rating: {{ number_format($review->rating_overall, 1) }} ★ (Lihat Ulasan)</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800">
            {{ $items->links() }}
        </div>
    @endif
</div>
@endsection
