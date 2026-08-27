@extends('layouts.admin')

@section('title', 'Manajemen Ulasan Film')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-black font-mono tracking-tight text-white flex items-center gap-2">
                <x-lucide-film class="w-6 h-6 text-amber-400" />
                <span>Manajemen Ulasan ({{ $reviews->total() }})</span>
            </h1>
            <p class="text-xs text-zinc-400 font-mono mt-0.5">
                Kelola daftar review, rating, dan catatan tontonan Anda.
            </p>
        </div>

        <a href="{{ route('admin.reviews.create') }}" 
           class="neo-btn px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-xs font-mono font-bold shadow-[3px_3px_0px_0px_#fff]">
            <x-lucide-plus class="w-4 h-4 mr-1.5" />
            <span>Tambah Ulasan Baru</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <input type="text" 
                   name="q" 
                   value="{{ request('q') }}" 
                   placeholder="Cari judul ulasan..."
                   class="w-full neo-input pl-9 pr-4 py-2 rounded-xl text-xs font-mono">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                <x-lucide-search class="w-4 h-4" />
            </div>
        </div>

        <select name="type" onchange="this.form.submit()" class="neo-input px-3 py-2 rounded-xl text-xs font-mono">
            <option value="">Semua Tipe</option>
            <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Film</option>
            <option value="series" {{ request('type') === 'series' ? 'selected' : '' }}>Series</option>
            <option value="anime" {{ request('type') === 'anime' ? 'selected' : '' }}>Anime</option>
        </select>

        @if(request()->hasAny(['q', 'type']))
            <a href="{{ route('admin.reviews.index') }}" class="neo-btn px-3 py-2 bg-zinc-800 text-zinc-300 rounded-xl text-xs font-mono border border-slate-700">
                Reset
            </a>
        @endif
    </form>

    <!-- Reviews Table -->
    <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)]">
        @if($reviews->isEmpty())
            <div class="p-12 text-center space-y-3">
                <x-lucide-film class="w-10 h-10 mx-auto text-zinc-600" />
                <p class="text-sm font-mono text-zinc-400">Belum ada data ulasan.</p>
                <a href="{{ route('admin.reviews.create') }}" class="inline-block px-4 py-2 bg-amber-400 text-black text-xs font-mono font-bold rounded-lg">
                    Tambah Ulasan Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs">
                    <thead class="bg-zinc-900/80 border-b border-slate-800 text-zinc-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Film / Series</th>
                            <th class="py-3.5 px-4">Tipe & Tahun</th>
                            <th class="py-3.5 px-4">Rating</th>
                            <th class="py-3.5 px-4">Tgl Nonton</th>
                            <th class="py-3.5 px-4">Platform</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($reviews as $rev)
                            @php $movie = $rev->movieSeries; @endphp
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $movie->poster_image_url }}" alt="" class="w-10 h-14 object-cover rounded-lg border border-slate-700 shrink-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('reviews.show', $movie->slug) }}" target="_blank" class="font-bold text-white hover:text-amber-400 text-sm truncate block">
                                                {{ $movie->title }}
                                            </a>
                                            @if($rev->headline)
                                                <p class="text-[11px] text-zinc-400 truncate italic">"{{ $rev->headline }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border border-black/30
                                        {{ $movie->type === 'movie' ? 'bg-cyan-500/20 text-cyan-300' : 'bg-purple-500/20 text-purple-300' }}">
                                        {{ strtoupper($movie->type) }}
                                    </span>
                                    <span class="text-zinc-400 ml-1">{{ $movie->release_year ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5 font-bold text-amber-400">
                                        <x-lucide-star class="w-4 h-4 fill-amber-400" />
                                        <span>{{ number_format($rev->rating_overall, 1) }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-zinc-300">
                                    {{ $rev->watched_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($rev->watch_platform)
                                        <span class="px-2 py-0.5 bg-zinc-800 text-zinc-300 rounded border border-slate-700 text-[10px]">
                                            {{ $rev->watch_platform }}
                                        </span>
                                    @else
                                        <span class="text-zinc-500">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- View Public -->
                                        <a href="{{ route('reviews.show', $movie->slug) }}" 
                                           target="_blank" 
                                           title="Lihat Halaman Publik"
                                           class="p-1.5 text-zinc-400 hover:text-cyan-400 bg-zinc-800 rounded hover:bg-zinc-700">
                                            <x-lucide-external-link class="w-3.5 h-3.5" />
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.reviews.edit', $rev) }}" 
                                           title="Edit Ulasan"
                                           class="p-1.5 text-zinc-400 hover:text-purple-400 bg-zinc-800 rounded hover:bg-zinc-700">
                                            <x-lucide-edit-3 class="w-3.5 h-3.5" />
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.reviews.destroy', $rev) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Hapus ulasan untuk {{ addslashes($movie->title) }}?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Ulasan"
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
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
