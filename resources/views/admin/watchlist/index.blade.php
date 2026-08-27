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

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
        <a href="{{ route('admin.watchlist.index') }}" 
           class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
           {{ $status === 'all' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
            Semua ({{ $counts['all'] }})
        </a>

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
                            <th class="py-3.5 px-4">Progress Episode</th>
                            <th class="py-3.5 px-4">Platform</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($items as $item)
                            @php 
                                $movie = $item->movieSeries;
                                $badge = $item->status_badge;
                            @endphp
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $movie->poster_image_url }}" alt="" class="w-10 h-14 object-cover rounded-lg border border-slate-700 shrink-0">
                                        <div class="min-w-0">
                                            <h4 class="font-bold text-white text-sm truncate">{{ $movie->title }}</h4>
                                            <span class="text-[11px] text-zinc-400">
                                                {{ strtoupper($movie->type) }} • {{ $movie->release_year ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 space-y-1">
                                    <form action="{{ route('admin.watchlist.status', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="neo-input px-2 py-1 rounded text-[11px] font-bold">
                                            <option value="plan_to_watch" {{ $item->status === 'plan_to_watch' ? 'selected' : '' }}>Rencana Nonton</option>
                                            <option value="watching" {{ $item->status === 'watching' ? 'selected' : '' }}>Sedang Menonton</option>
                                            <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Selesai</option>
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

                                <td class="py-3 px-4">
                                    @if($movie->type !== 'movie')
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-emerald-400">S{{ $item->current_season }} Ep {{ $item->current_episode }}</span>
                                            
                                            <div class="flex items-center gap-1">
                                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="w-6 h-6 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 rounded flex items-center justify-center border border-slate-700 font-bold">
                                                        -
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.watchlist.progress', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="w-6 h-6 bg-emerald-500 hover:bg-emerald-400 text-black rounded flex items-center justify-center border border-black font-bold shadow-[1px_1px_0px_#fff]">
                                                        +
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-zinc-500">Film Tunggal</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-zinc-300">
                                    {{ $item->watch_platform ?? '-' }}
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Convert to Review Button -->
                                        <a href="{{ route('admin.reviews.create', ['watchlist_id' => $item->id]) }}" 
                                           title="Tandai Selesai & Buat Ulasan"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-400 hover:bg-amber-300 text-black font-bold rounded-lg border border-black shadow-[2px_2px_0px_#fff] transition-all">
                                            <x-lucide-award class="w-3.5 h-3.5" />
                                            <span>Ulas</span>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.watchlist.destroy', $item) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Hapus dari watchlist?')" 
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
