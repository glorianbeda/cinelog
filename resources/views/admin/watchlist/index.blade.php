@extends('layouts.admin')

@section('title', 'Manajemen Watchlist')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-black font-mono tracking-tight text-white flex items-center gap-2">
                <x-lucide-bookmark class="w-6 h-6 text-cyan-400" />
                <span>Manajemen Watchlist ({{ $counts['all'] }})</span>
            </h1>
            <p class="text-xs text-zinc-400 font-mono mt-0.5">
                Kelola antrean film dan pelacak progres episode serial tontonan Anda.
            </p>
        </div>

        <a href="{{ route('admin.watchlist.create') }}" 
           class="neo-btn px-4 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black text-xs font-mono font-bold shadow-[3px_3px_0px_0px_#fff]">
            <x-lucide-plus class="w-4 h-4 mr-1.5" />
            <span>Tambah ke Watchlist</span>
        </a>
    </div>

    <!-- Needs Review Alert Banner -->
    @if(($counts['needs_review'] ?? 0) > 0 && $status !== 'needs_review')
        <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/20 via-purple-500/10 to-transparent border-2 border-amber-400/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[3px_3px_0px_#F59E0B]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-400 text-black flex items-center justify-center font-bold text-lg shrink-0 shadow-[1px_1px_0px_#000]">
                    ⭐
                </div>
                <div>
                    <h4 class="font-bold text-white font-mono text-sm">
                        {{ $counts['needs_review'] }} Tontonan Selesai Menunggu Ulasan!
                    </h4>
                    <p class="text-xs text-zinc-300 font-mono">
                        Item yang telah rampung ditonton siap diberi rating dan ulasan lengkap untuk katalog Anda.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.watchlist.index', ['status' => 'needs_review']) }}" 
               class="neo-btn px-3.5 py-2 rounded-lg bg-amber-400 hover:bg-amber-300 text-black text-xs font-mono font-black shrink-0 border border-black shadow-[2px_2px_0px_#fff]">
                Lihat Siap Diulas →
            </a>
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
        <a href="{{ route('admin.watchlist.index') }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'all' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Semua ({{ $counts['all'] }})
        </a>

        @if(($counts['needs_review'] ?? 0) > 0)
            <a href="{{ route('admin.watchlist.index', ['status' => 'needs_review']) }}" 
               class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all flex items-center gap-1.5
               {{ $status === 'needs_review' ? 'bg-amber-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-amber-950/40 text-amber-300 border-amber-500/50 hover:bg-amber-900/50' }}">
                <span>⭐ Siap Diulas</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'needs_review' ? 'bg-black text-amber-400' : 'bg-amber-400 text-black font-black' }}">
                    {{ $counts['needs_review'] }}
                </span>
            </a>
        @endif

        <a href="{{ route('admin.watchlist.index', ['status' => 'watching']) }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'watching' ? 'bg-emerald-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Sedang Menonton ({{ $counts['watching'] }})
        </a>

        <a href="{{ route('admin.watchlist.index', ['status' => 'plan_to_watch']) }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'plan_to_watch' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Rencana Nonton ({{ $counts['plan_to_watch'] }})
        </a>

        <a href="{{ route('admin.watchlist.index', ['status' => 'completed']) }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'completed' ? 'bg-purple-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Selesai ({{ $counts['completed'] }})
        </a>

        <a href="{{ route('admin.watchlist.index', ['status' => 'on_hold']) }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'on_hold' ? 'bg-amber-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Tertunda ({{ $counts['on_hold'] }})
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)]">
        @if($items->isEmpty())
            <div class="p-12 text-center space-y-3">
                <x-lucide-bookmark-x class="w-10 h-10 mx-auto text-zinc-600" />
                <p class="text-sm font-mono text-zinc-400">Tidak ada item watchlist pada kategori ini.</p>
                <a href="{{ route('admin.watchlist.create') }}" class="inline-block px-4 py-2 bg-cyan-400 text-black text-xs font-mono font-bold rounded-lg">
                    Tambah Tontonan Baru
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead class="bg-zinc-900/80 border-b border-slate-800 text-zinc-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Judul Film / Series</th>
                            <th class="py-3.5 px-4">Status & Prioritas</th>
                            <th class="py-3.5 px-4">Progress Nonton</th>
                            <th class="py-3.5 px-4">Platform / Catatan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($items as $item)
                            @php 
                                $movie = $item->movieSeries;
                                $badge = $item->status_badge;
                                $isFinished = $item->is_finished;
                                $needsReview = $item->needs_review;
                                $pct = $item->progress_percentage;
                            @endphp
                            <tr class="hover:bg-zinc-800/30 transition-colors {{ $needsReview ? 'bg-amber-950/10' : '' }}">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $movie->poster_image_url }}" alt="" class="w-10 h-14 object-cover rounded-lg border border-slate-700 shrink-0">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h4 class="font-bold text-white text-sm truncate max-w-[200px] sm:max-w-xs">{{ $movie->title }}</h4>
                                                @if($isFinished)
                                                    <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[9px] font-bold">
                                                        ✓ Usai
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-zinc-400 block mt-0.5">
                                                {{ strtoupper($movie->type) }} • {{ $movie->release_year ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 space-y-1.5">
                                    <form action="{{ route('admin.watchlist.status', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="neo-input px-2 py-1 rounded text-[11px] font-bold {{ $item->status === 'completed' ? 'border-purple-500 text-purple-300' : '' }}">
                                            <option value="plan_to_watch" {{ $item->status === 'plan_to_watch' ? 'selected' : '' }}>Rencana Nonton</option>
                                            <option value="watching" {{ $item->status === 'watching' ? 'selected' : '' }}>Sedang Menonton</option>
                                            <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Selesai (Usai)</option>
                                            <option value="on_hold" {{ $item->status === 'on_hold' ? 'selected' : '' }}>Tertunda / On Hold</option>
                                            <option value="dropped" {{ $item->status === 'dropped' ? 'selected' : '' }}>Dropped</option>
                                        </select>
                                    </form>

                                    @if($item->priority === 'high')
                                        <span class="inline-block px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[10px] font-bold">
                                            High Priority
                                        </span>
                                    @endif
                                </td>

                                <!-- Progress Episode & Visual Bar -->
                                <td class="py-3 px-4">
                                    @if($movie->type !== 'movie')
                                        <div class="space-y-1.5 min-w-[150px]">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="font-bold {{ $isFinished ? 'text-purple-300' : 'text-emerald-400' }}">
                                                    S{{ $item->current_season }} Ep {{ $item->current_episode }}
                                                    @if($movie->total_episodes)
                                                        <span class="text-zinc-500 font-normal">/ {{ $movie->total_episodes }}</span>
                                                    @endif
                                                </span>
                                                <span class="text-[10px] font-mono text-zinc-400">{{ $pct }}%</span>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="w-full bg-zinc-800/90 rounded-full h-2 border border-slate-700 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300 {{ $isFinished ? 'bg-gradient-to-r from-purple-400 to-emerald-400' : 'bg-gradient-to-r from-cyan-400 to-emerald-400' }}" 
                                                     style="width: {{ $pct }}%">
                                                </div>
                                            </div>

                                            <!-- Episode Stepper Buttons -->
                                            <div class="flex items-center gap-1 pt-0.5">
                                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" 
                                                            title="Kurang 1 episode"
                                                            class="w-6 h-5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded flex items-center justify-center border border-slate-700 font-bold text-xs">
                                                        -
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" 
                                                            title="Tambah 1 episode"
                                                            class="w-6 h-5 {{ $isFinished ? 'bg-zinc-700 text-zinc-400' : 'bg-emerald-500 hover:bg-emerald-400 text-black shadow-[1px_1px_0px_#fff]' }} rounded flex items-center justify-center border border-black font-bold text-xs">
                                                        +
                                                    </button>
                                                </form>

                                                @if(!$isFinished)
                                                    <form action="{{ route('admin.watchlist.status', $item) }}" method="POST" class="inline ml-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" 
                                                                title="Tandai Selesai Menonton"
                                                                class="px-1.5 py-0.5 text-[9px] bg-zinc-800 hover:bg-purple-900/50 text-purple-300 rounded border border-slate-700">
                                                            Tandai Selesai
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Single Movie Progress -->
                                        <div class="space-y-1.5 min-w-[130px]">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="text-zinc-400">🎬 Film Tunggal</span>
                                                <span class="text-[10px] font-mono text-zinc-400">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full bg-zinc-800/90 rounded-full h-2 border border-slate-700 overflow-hidden">
                                                <div class="h-full rounded-full {{ $isFinished ? 'bg-purple-400' : ($item->status === 'watching' ? 'bg-cyan-400' : 'bg-zinc-600') }}" 
                                                     style="width: {{ $pct }}%">
                                                </div>
                                            </div>
                                            @if(!$isFinished)
                                                <form action="{{ route('admin.watchlist.status', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="text-[10px] text-purple-400 hover:underline">
                                                        ✓ Tandai Sudah Nonton
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-zinc-300">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-white block">{{ $item->watch_platform ?: '-' }}</span>
                                        @if($item->notes)
                                            <p class="text-[10px] text-zinc-400 italic truncate max-w-[150px]" title="{{ $item->notes }}">
                                                "{{ $item->notes }}"
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if($needsReview)
                                            <!-- Glowing Highlighted Review Button when Finished & Not Yet Reviewed -->
                                            <a href="{{ route('admin.reviews.create', ['watchlist_id' => $item->id]) }}" 
                                               title="Tontonan Selesai! Klik untuk Buat Ulasan & Beri Rating"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-400 hover:bg-amber-300 text-black font-black rounded-xl border-2 border-black shadow-[2px_2px_0px_#fff] ring-2 ring-amber-400/70 hover:scale-105 transition-all">
                                                <x-lucide-star class="w-3.5 h-3.5 fill-black" />
                                                <span>Beri Rating</span>
                                            </a>
                                        @elseif($movie->reviews->isNotEmpty())
                                            <!-- Already Reviewed Button -->
                                            <a href="{{ route('reviews.show', $movie->slug) }}" 
                                               target="_blank"
                                               title="Lihat Ulasan di Katalog"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-cyan-300 text-xs rounded-lg border border-slate-700 transition-all">
                                                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-400" />
                                                <span>Diulas</span>
                                            </a>
                                        @else
                                            <!-- Standard Review Button -->
                                            <a href="{{ route('admin.reviews.create', ['watchlist_id' => $item->id]) }}" 
                                               title="Buat Ulasan"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-zinc-800 hover:bg-amber-400 hover:text-black text-zinc-300 font-bold rounded-lg border border-slate-700 transition-all">
                                                <x-lucide-award class="w-3.5 h-3.5" />
                                                <span>Ulas</span>
                                            </a>
                                        @endif

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.watchlist.destroy', $item) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Hapus item ini dari watchlist?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus dari Watchlist"
                                                    class="p-1.5 text-zinc-400 hover:text-rose-400 bg-zinc-800 rounded hover:bg-rose-950/40">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-800">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
