@extends('layouts.app')

@section('title', 'Katalog Ulasan & Rating Film')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b-2 border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/40 text-xs font-mono font-bold mb-2">
                <x-lucide-film class="w-3.5 h-3.5" />
                <span>Katalog Arsip Ulasan</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white">
                SEMUA <span class="text-amber-400">ULASAN</span>
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 font-mono mt-1">
                Menampilkan {{ $reviews->total() }} judul film dan series yang telah dinilai.
            </p>
        </div>

        <!-- Live Search Form -->
        <form action="{{ route('catalog.index') }}" method="GET" class="w-full md:w-80">
            <!-- Preserve other filters -->
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            @if(request('genre')) <input type="hidden" name="genre" value="{{ request('genre') }}"> @endif
            @if(request('min_rating')) <input type="hidden" name="min_rating" value="{{ request('min_rating') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="relative">
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari judul, sutradara, kata kunci..."
                       class="w-full neo-input pl-9 pr-8 py-2 rounded-xl text-xs font-mono shadow-[2px_2px_0px_#A855F7]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                    <x-lucide-search class="w-4 h-4" />
                </div>
                @if(request('q'))
                    <a href="{{ route('catalog.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-white">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Filters Bar -->
    <div class="bg-[#14141E] border-2 border-slate-700 rounded-2xl p-4 sm:p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4">
        <!-- Filter Pills: Type -->
        <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
            <span class="text-zinc-400 font-bold uppercase tracking-wider mr-1">Tipe:</span>
            
            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" 
               class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
               {{ !request('type') ? 'bg-amber-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
                Semua
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['type' => 'movie']) }}" 
               class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
               {{ request('type') === 'movie' ? 'bg-cyan-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
                Film (Movie)
            </a>

            <a href="{{ request()->fullUrlWithQuery(['type' => 'series']) }}" 
               class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
               {{ request('type') === 'series' ? 'bg-purple-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
                Serial (Series)
            </a>

            <a href="{{ request()->fullUrlWithQuery(['type' => 'anime']) }}" 
               class="px-3 py-1.5 rounded-lg border-2 font-bold transition-all
               {{ request('type') === 'anime' ? 'bg-rose-400 text-black border-black shadow-[2px_2px_0px_#fff]' : 'bg-zinc-900 text-zinc-400 border-slate-700 hover:text-white' }}">
                Anime
            </a>
        </div>

        <!-- Dropdowns Row: Genre, Rating, Sort -->
        <form action="{{ route('catalog.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-800 text-xs font-mono">
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif

            <!-- Genre Dropdown -->
            <div>
                <label class="block text-zinc-400 font-bold mb-1">Genre</label>
                <select name="genre" onchange="this.form.submit()" class="w-full neo-input px-2.5 py-1.5 rounded-lg text-xs">
                    <option value="">Semua Genre</option>
                    @foreach($genres as $genre)
                        <option value="{{ $genre->slug }}" {{ request('genre') === $genre->slug ? 'selected' : '' }}>
                            {{ $genre->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Rating Dropdown -->
            <div>
                <label class="block text-zinc-400 font-bold mb-1">Minimum Rating</label>
                <select name="min_rating" onchange="this.form.submit()" class="w-full neo-input px-2.5 py-1.5 rounded-lg text-xs">
                    <option value="">Semua Rating</option>
                    <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>★★★★½ 4.5+ (Masterpiece)</option>
                    <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>★★★★☆ 4.0+ (Great)</option>
                    <option value="3.0" {{ request('min_rating') == '3.0' ? 'selected' : '' }}>★★★☆☆ 3.0+ (Good)</option>
                    <option value="2.0" {{ request('min_rating') == '2.0' ? 'selected' : '' }}>★★☆☆☆ 2.0+ (Decent)</option>
                </select>
            </div>

            <!-- Year Dropdown -->
            <div>
                <label class="block text-zinc-400 font-bold mb-1">Tahun Rilis</label>
                <select name="year" onchange="this.form.submit()" class="w-full neo-input px-2.5 py-1.5 rounded-lg text-xs">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Dropdown -->
            <div>
                <label class="block text-zinc-400 font-bold mb-1">Urutan</label>
                <select name="sort" onchange="this.form.submit()" class="w-full neo-input px-2.5 py-1.5 rounded-lg text-xs">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru Diulas</option>
                    <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                    <option value="rating_asc" {{ request('sort') === 'rating_asc' ? 'selected' : '' }}>Rating Terendah</option>
                    <option value="year_desc" {{ request('sort') === 'year_desc' ? 'selected' : '' }}>Tahun Terbaru</option>
                    <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Judul (A-Z)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Active Filters Reset Badge -->
    @if(request()->hasAny(['type', 'genre', 'min_rating', 'year', 'sort', 'q']))
        <div class="flex items-center gap-2 text-xs font-mono">
            <span class="text-zinc-400">Filter Aktif:</span>
            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30 transition-colors">
                <x-lucide-rotate-ccw class="w-3 h-3" />
                <span>Reset Semua Filter</span>
            </a>
        </div>
    @endif

    <!-- Reviews Grid -->
    @if($reviews->isEmpty())
        <div class="p-16 text-center bg-[#14141E] border-2 border-dashed border-slate-700 rounded-2xl space-y-4">
            <x-lucide-search-x class="w-12 h-12 mx-auto text-zinc-600" />
            <h3 class="text-lg font-bold text-zinc-300 font-mono">Tidak ditemukan ulasan yang cocok</h3>
            <p class="text-xs text-zinc-500 font-mono">Coba ubah kata kunci pencarian atau reset filter yang sedang aktif.</p>
            <a href="{{ route('catalog.index') }}" class="inline-block px-4 py-2 bg-amber-400 text-black text-xs font-mono font-bold rounded-lg border-2 border-black">
                Reset Filter
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($reviews as $review)
                <x-movie-card :review="$review" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 pt-6 border-t border-slate-800">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
