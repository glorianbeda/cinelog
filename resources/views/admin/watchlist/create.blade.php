@extends('layouts.admin')

@section('title', 'Tambah ke Watchlist')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-black font-mono tracking-tight text-white flex items-center gap-2">
                <x-lucide-bookmark-plus class="w-6 h-6 text-cyan-400" />
                <span>Tambah Judul ke Watchlist</span>
            </h1>
            <p class="text-xs text-zinc-400 font-mono mt-0.5">
                Cari judul film/series di TMDB untuk menambahkan ke antrean tontonan.
            </p>
        </div>

        <a href="{{ route('admin.watchlist.index') }}" class="neo-btn px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 text-xs font-mono border border-slate-700">
            <x-lucide-arrow-left class="w-3.5 h-3.5 mr-1" />
            <span>Kembali</span>
        </a>
    </div>

    <!-- TMDB Live Searcher -->
    <div x-data="tmdbSearcher()" class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-5 shadow-[6px_6px_0px_0px_#06B6D4] space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-cyan-400 font-mono text-xs font-bold uppercase tracking-wider">
                <x-lucide-database class="w-4 h-4" />
                <span>Pencarian Cepat TMDB Open API</span>
            </div>
        </div>

        <div class="relative">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" 
                           x-model="query" 
                           @input.debounce.400ms="search()"
                           @keydown.escape="isOpen = false"
                           placeholder="Ketik judul film/series..."
                           class="w-full neo-input pl-10 pr-4 py-3 rounded-xl text-sm font-mono placeholder:text-zinc-500">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <template x-if="!loading">
                            <x-lucide-search class="w-5 h-5 text-cyan-400" />
                        </template>
                        <template x-if="loading">
                            <x-lucide-loader-2 class="w-5 h-5 text-cyan-400 animate-spin" />
                        </template>
                    </div>
                </div>

                <select x-model="type" @change="search()" class="neo-input px-3 py-3 rounded-xl text-xs font-mono">
                    <option value="all">Semua Format</option>
                    <option value="movie">Film Saja</option>
                    <option value="tv">Series Saja</option>
                </select>
            </div>

            <!-- Dropdown Results -->
            <div x-show="isOpen && results.length > 0" 
                 @click.outside="isOpen = false"
                 class="absolute z-50 left-0 right-0 mt-2 bg-[#12121A] border-2 border-slate-600 rounded-xl shadow-2xl max-h-80 overflow-y-auto divide-y divide-slate-800">
                <template x-for="item in results" :key="item.tmdb_id">
                    <button type="button" 
                            @click="selectResult(item)"
                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-zinc-800/80 transition-colors group">
                        <img :src="item.poster_url || 'https://placehold.co/80x120/1a1a24/ffffff?text=No+Poster'" 
                             class="w-10 h-14 object-cover rounded border border-slate-700 shrink-0">
                        <div class="min-w-0 flex-1 font-mono text-xs">
                            <h4 class="font-bold text-white group-hover:text-cyan-400 truncate" x-text="item.title"></h4>
                            <div class="flex items-center gap-2 text-zinc-400 text-[11px] mt-0.5">
                                <span class="px-1.5 py-0.2 bg-zinc-800 text-cyan-300 rounded font-bold uppercase" x-text="item.type"></span>
                                <span x-text="item.release_year || 'N/A'"></span>
                            </div>
                        </div>
                        <div class="shrink-0 text-cyan-400 font-mono text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                            Pilih ➔
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Main Watchlist Form -->
    <form action="{{ route('admin.watchlist.store') }}" method="POST" x-data="formDraft('cinelog_draft_watchlist_create')" class="space-y-8">
        @csrf

        <!-- DRAFT AUTO-SAVE NOTIFICATION BANNER -->
        <template x-if="hasDraft">
            <div class="p-4 bg-[#083344]/90 border-2 border-cyan-500 rounded-2xl shadow-[6px_6px_0px_0px_#06B6D4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/20 text-cyan-400 shrink-0 border border-cyan-500/40">
                        <x-lucide-sparkles class="w-5 h-5 text-cyan-400" />
                    </div>
                    <div class="font-mono">
                        <div class="text-xs font-bold text-white flex items-center gap-2">
                            <span>Draf Watchlist Tersimpan Ditemukan</span>
                            <span class="px-2 py-0.5 rounded bg-cyan-500/30 text-cyan-300 text-[10px]" x-text="'Tersimpan: ' + savedAt"></span>
                        </div>
                        <p class="text-[11px] text-zinc-300 mt-0.5 font-sans">
                            Data antrean watchlist yang Anda ketik sebelumnya tersimpan secara lokal di browser.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <button type="button" 
                            @click="restoreDraft()" 
                            class="neo-btn px-3.5 py-1.5 rounded-lg bg-cyan-400 hover:bg-cyan-300 text-black text-xs font-mono font-bold shadow-[2px_2px_0px_#fff] flex items-center gap-1.5">
                        <x-lucide-history class="w-3.5 h-3.5" />
                        <span>Pulihkan Draf</span>
                    </button>
                    <button type="button" 
                            @click="discardDraft()" 
                            title="Buang Draf"
                            class="neo-btn p-1.5 rounded-lg bg-zinc-800 hover:bg-rose-950 hover:text-rose-400 text-zinc-400 text-xs font-mono border border-slate-700">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </template>

        <!-- Toast Feedback Message -->
        <template x-if="toastMessage">
            <div class="p-3 bg-emerald-500/20 border-2 border-emerald-500 rounded-xl text-emerald-300 font-mono text-xs flex items-center justify-between shadow-[4px_4px_0px_0px_#10B981]">
                <div class="flex items-center gap-2">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-400" />
                    <span x-text="toastMessage"></span>
                </div>
                <button type="button" @click="toastMessage = ''" class="text-emerald-400 hover:text-white">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            </div>
        </template>

        <input type="hidden" id="input_tmdb_id" name="tmdb_id" value="{{ old('tmdb_id') }}">

        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#06B6D4] space-y-6">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                <x-lucide-film class="w-4 h-4" />
                <span>Detail Tontonan</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-6">
                <!-- Poster Preview -->
                <div class="sm:col-span-3 space-y-2">
                    <label class="block text-xs font-mono font-bold text-zinc-300">Preview Poster</label>
                    <div class="w-full aspect-[2/3] bg-zinc-900 border-2 border-slate-700 rounded-xl overflow-hidden flex items-center justify-center">
                        <img id="poster_preview" 
                             src="{{ old('poster_url') }}" 
                             alt="Poster"
                             class="{{ old('poster_url') ? '' : 'hidden' }} w-full h-full object-cover">
                    </div>
                </div>

                <!-- Fields -->
                <div class="sm:col-span-9 space-y-4 font-mono text-xs">
                    <!-- Title & Type -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="input_title" class="block font-bold text-zinc-200 mb-1">
                                Judul Film / Series <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="input_title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   class="w-full neo-input px-3.5 py-2 rounded-lg text-sm font-sans font-bold">
                        </div>

                    <!-- Type, Year, Director, Duration & Series Details -->
                    <div x-data="{
                        type: '{{ old('type', 'movie') }}',
                        runtime: '{{ old('runtime_minutes', '') }}',
                        seasons: '{{ old('total_seasons', '') }}',
                        episodes: '{{ old('total_episodes', '') }}',
                        get isSeries() {
                            return this.type === 'series' || this.type === 'anime';
                        },
                        get totalEstimatedMinutes() {
                            const r = parseInt(this.runtime) || 0;
                            if (!this.isSeries) return r;
                            const eps = parseInt(this.episodes) || 1;
                            return r * eps;
                        },
                        get formattedCalculation() {
                            const total = this.totalEstimatedMinutes;
                            if (!total) return '';
                            const h = Math.floor(total / 60);
                            const m = total % 60;
                            if (!this.isSeries) {
                                return h > 0 ? `${h} jam ${m > 0 ? m + ' menit' : ''}` : `${m} menit`;
                            }
                            const eps = parseInt(this.episodes) || 1;
                            const r = parseInt(this.runtime) || 0;
                            return `Total: ${total} menit (${h > 0 ? h + ' jam ' : ''}${m > 0 ? m + ' mnt' : ''}) [${eps} eps × ${r} mnt]`;
                        }
                    }" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="input_type" class="block font-bold text-zinc-200 mb-1">
                                    Format Tontonan <span class="text-rose-500">*</span>
                                </label>
                                <select id="input_type" 
                                        name="type" 
                                        x-model="type" 
                                        required 
                                        class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                    <option value="movie" {{ old('type') === 'movie' ? 'selected' : '' }}>Film (Movie)</option>
                                    <option value="series" {{ old('type') === 'series' ? 'selected' : '' }}>Serial (Series)</option>
                                    <option value="anime" {{ old('type') === 'anime' ? 'selected' : '' }}>Anime</option>
                                </select>
                            </div>

                            <div>
                                <label for="input_release_year" class="block font-bold text-zinc-200 mb-1">
                                    Tahun Rilis
                                </label>
                                <input type="number" 
                                       id="input_release_year" 
                                       name="release_year" 
                                       value="{{ old('release_year') }}" 
                                       class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                            </div>

                            <div>
                                <label for="input_director" class="block font-bold text-zinc-200 mb-1">
                                    Sutradara / Kreator
                                </label>
                                <input type="text" 
                                       id="input_director" 
                                       name="director" 
                                       value="{{ old('director') }}" 
                                       class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                            </div>
                        </div>

                        <!-- Duration / Seasons / Episodes -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-3.5 bg-zinc-900/60 border border-slate-800 rounded-xl">
                            <div>
                                <label for="input_runtime_minutes" class="block font-bold text-zinc-200 mb-1 flex items-center justify-between">
                                    <span x-text="isSeries ? '⏱️ Menit / Episode' : '⏱️ Durasi Menit Total'"></span>
                                </label>
                                <input type="number" 
                                       id="input_runtime_minutes" 
                                       name="runtime_minutes" 
                                       x-model="runtime"
                                       :placeholder="isSeries ? 'Contoh: 45 (atau 24)' : 'Contoh: 148'"
                                       class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                <span class="text-[10px] text-zinc-400 mt-1 block" 
                                      x-text="isSeries ? 'Perkiraan durasi 1 episode (menit)' : 'Durasi total film dalam menit'"></span>
                            </div>

                            <div x-show="isSeries">
                                <label for="input_total_seasons" class="block font-bold text-zinc-400 mb-1">
                                    Total Season
                                </label>
                                <input type="number" 
                                       id="input_total_seasons" 
                                       name="total_seasons" 
                                       x-model="seasons"
                                       placeholder="Contoh: 3"
                                       class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                <span class="text-[10px] text-zinc-400 mt-1 block" x-show="isSeries">Jumlah season tontonan</span>
                            </div>

                            <div x-show="isSeries">
                                <label for="input_total_episodes" class="block font-bold text-zinc-200 mb-1">
                                    Total Episode <span x-show="isSeries" class="text-amber-400 font-normal text-[10px]">(ke jam)</span>
                                </label>
                                <input type="number" 
                                       id="input_total_episodes" 
                                       name="total_episodes" 
                                       x-model="episodes"
                                       placeholder="Contoh: 24"
                                       class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                <span class="text-[10px] text-zinc-400 mt-1 block" x-show="isSeries">Total episode yang ditonton</span>
                            </div>

                            <!-- Live Calculation Helper -->
                            <div class="sm:col-span-3 pt-2 border-t border-slate-800 flex items-center justify-between" x-show="formattedCalculation">
                                <span class="text-[11px] font-mono text-zinc-400 flex items-center gap-1.5">
                                    <x-lucide-calculator class="w-3.5 h-3.5 text-cyan-400" />
                                    <span>Kalkulasi Jam Menonton:</span>
                                </span>
                                <span class="text-xs font-mono font-bold text-cyan-300 bg-cyan-950/60 px-2.5 py-0.5 rounded border border-cyan-500/40" 
                                      x-text="formattedCalculation">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Poster & Backdrop -->
                    <input type="hidden" id="input_poster_url" name="poster_url" value="{{ old('poster_url') }}">
                    <input type="hidden" id="input_backdrop_url" name="backdrop_url" value="{{ old('backdrop_url') }}">

                    <!-- Synopsis -->
                    <div>
                        <label for="input_synopsis" class="block font-bold text-zinc-400 mb-1">
                            Sinopsis
                        </label>
                        <textarea id="input_synopsis" 
                                  name="synopsis" 
                                  rows="3" 
                                  class="w-full neo-input px-3.5 py-2 rounded-lg text-xs font-sans">{{ old('synopsis') }}</textarea>
                    </div>

                    <!-- Watchlist Settings: Status, Priority, Progress, Platform -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-slate-800">
                        <div>
                            <label for="status" class="block font-bold text-cyan-400 mb-1">
                                Status Watchlist <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" required class="w-full neo-input px-3 py-2 rounded-lg text-xs font-bold">
                                <option value="plan_to_watch">Rencana Nonton</option>
                                <option value="watching">Sedang Menonton</option>
                                <option value="on_hold">Tertunda / On Hold</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block font-bold text-cyan-400 mb-1">
                                Prioritas <span class="text-rose-500">*</span>
                            </label>
                            <select id="priority" name="priority" required class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                <option value="medium">Sedang (Medium)</option>
                                <option value="high">Tinggi (High Priority)</option>
                                <option value="low">Rendah (Low)</option>
                            </select>
                        </div>

                        <div>
                            <label for="watch_platform" class="block font-bold text-zinc-200 mb-1">
                                Tempat Menonton
                            </label>
                            <input type="text" 
                                   id="watch_platform" 
                                   name="watch_platform" 
                                   placeholder="Contoh: Netflix, Bioskop, Prime"
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <!-- Initial Season / Episode (Series Only) -->
                    <div x-show="isSeries" class="grid grid-cols-2 gap-4 p-3.5 bg-zinc-900/50 border border-slate-800 rounded-xl">
                        <div>
                            <label for="current_season" class="block font-bold text-zinc-200 mb-1">
                                Mulai dari Season
                            </label>
                            <input type="number" 
                                   id="current_season" 
                                   name="current_season" 
                                   value="{{ old('current_season', 1) }}" 
                                   min="1"
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                            <span class="text-[10px] text-zinc-400 mt-0.5 block">Nomor season saat ini</span>
                        </div>

                        <div>
                            <label for="current_episode" class="block font-bold text-zinc-200 mb-1">
                                Mulai dari Episode
                            </label>
                            <input type="number" 
                                   id="current_episode" 
                                   name="current_episode" 
                                   value="{{ old('current_episode', 0) }}" 
                                   min="0"
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                            <span class="text-[10px] text-zinc-400 mt-0.5 block">Episode terakhir yang sudah ditonton</span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block font-bold text-zinc-400 mb-1">
                            Catatan Khusus Watchlist
                        </label>
                        <input type="text" 
                               id="notes" 
                               name="notes" 
                               placeholder="Contoh: Rekomendasi dari teman, tunggu tayang di streaming"
                               class="w-full neo-input px-3.5 py-2 rounded-lg text-xs font-sans">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button & Live Draft Indicator -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
            <!-- Auto-Save Status Indicator -->
            <div class="flex items-center gap-2 text-xs font-mono text-zinc-400">
                <template x-if="saveStatus === 'saving'">
                    <span class="flex items-center gap-1.5 text-amber-400">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span>Menyimpan draf di browser...</span>
                    </span>
                </template>
                <template x-if="saveStatus === 'saved'">
                    <span class="flex items-center gap-1.5 text-cyan-400">
                        <x-lucide-check class="w-3.5 h-3.5" />
                        <span>Draf tersimpan di browser (<span x-text="savedAt"></span>)</span>
                    </span>
                </template>
                <template x-if="saveStatus === 'idle'">
                    <span class="flex items-center gap-1.5 text-zinc-500 text-[11px]">
                        <x-lucide-shield-check class="w-3.5 h-3.5 text-zinc-600" />
                        <span>Auto-save browser aktif</span>
                    </span>
                </template>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.watchlist.index') }}" class="neo-btn px-5 py-3 rounded-xl bg-zinc-800 text-zinc-300 font-mono text-sm border border-slate-700">
                    Batal
                </a>

                <button type="submit" class="neo-btn px-8 py-3 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-black text-sm font-bold font-mono shadow-[4px_4px_0px_0px_#fff]">
                    <x-lucide-bookmark-check class="w-4 h-4 mr-2" />
                    <span>Simpan ke Watchlist</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
