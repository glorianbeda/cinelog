@extends('layouts.admin')

@section('title', 'Edit Ulasan: ' . $review->movieSeries->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-black font-mono tracking-tight text-white flex items-center gap-2">
                <x-lucide-edit-3 class="w-6 h-6 text-purple-400" />
                <span>Edit Ulasan: {{ $review->movieSeries->title }}</span>
            </h1>
            <p class="text-xs text-zinc-400 font-mono mt-0.5">
                Perbarui metadata film, rating, atau teks ulasan Anda.
            </p>
        </div>

        <a href="{{ route('admin.reviews.index') }}" class="neo-btn px-3 py-1.5 rounded-lg bg-zinc-800 text-zinc-300 text-xs font-mono border border-slate-700">
            <x-lucide-arrow-left class="w-3.5 h-3.5 mr-1" />
            <span>Kembali</span>
        </a>
    </div>

    @php $movie = $review->movieSeries; @endphp

    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" x-data="formDraft('cinelog_draft_review_edit_{{ $review->id }}')" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- DRAFT AUTO-SAVE NOTIFICATION BANNER -->
        <template x-if="hasDraft">
            <div class="p-4 bg-[#1e1b4b]/90 border-2 border-indigo-500 rounded-2xl shadow-[6px_6px_0px_0px_#6366F1] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-indigo-500/20 text-indigo-400 shrink-0 border border-indigo-500/40">
                        <x-lucide-sparkles class="w-5 h-5 text-indigo-400" />
                    </div>
                    <div class="font-mono">
                        <div class="text-xs font-bold text-white flex items-center gap-2">
                            <span>Draf Edit Tersimpan di Browser Ditemukan</span>
                            <span class="px-2 py-0.5 rounded bg-indigo-500/30 text-indigo-300 text-[10px]" x-text="'Tersimpan: ' + savedAt"></span>
                        </div>
                        <p class="text-[11px] text-zinc-300 mt-0.5 font-sans">
                            Perubahan belum tersimpan sebelumnya tersedia di memori lokal browser.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <button type="button" 
                            @click="restoreDraft()" 
                            class="neo-btn px-3.5 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-black text-xs font-mono font-bold shadow-[2px_2px_0px_#fff] flex items-center gap-1.5">
                        <x-lucide-history class="w-3.5 h-3.5" />
                        <span>Pulihkan Perubahan</span>
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

        <input type="hidden" id="input_cast_members" name="cast_members" value="{{ old('cast_members', json_encode($movie->cast_members ?? [])) }}">

        <!-- SECTION A: METADATA FILM/SERIES -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#A855F7] space-y-6">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-purple-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                <x-lucide-clapperboard class="w-4 h-4" />
                <span>Informasi Metadata Film / Series</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-6">
                <!-- Poster Preview -->
                <div class="sm:col-span-3 space-y-2">
                    <label class="block text-xs font-mono font-bold text-zinc-300">Preview Poster</label>
                    <div class="w-full aspect-[2/3] bg-zinc-900 border-2 border-slate-700 rounded-xl overflow-hidden flex items-center justify-center">
                        <img id="poster_preview" 
                             src="{{ old('poster_url', $movie->poster_image_url) }}" 
                             alt="Poster"
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Metadata Inputs -->
                <div class="sm:col-span-9 space-y-4 font-mono text-xs">
                    <!-- Title & Original Title -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="input_title" class="block font-bold text-zinc-200 mb-1">
                                Judul Film / Series <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="input_title" 
                                   name="title" 
                                   value="{{ old('title', $movie->title) }}" 
                                   required 
                                   class="w-full neo-input px-3.5 py-2 rounded-lg text-sm font-sans font-bold">
                        </div>

                        <div>
                            <label for="input_original_title" class="block font-bold text-zinc-400 mb-1">
                                Judul Asli (Opsional)
                            </label>
                            <input type="text" 
                                   id="input_original_title" 
                                   name="original_title" 
                                   value="{{ old('original_title', $movie->original_title) }}" 
                                   class="w-full neo-input px-3.5 py-2 rounded-lg text-sm font-sans">
                        </div>
                    </div>

                    <!-- Type, Release Year, Director -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="input_type" class="block font-bold text-zinc-200 mb-1">
                                Tipe Tontonan <span class="text-rose-500">*</span>
                            </label>
                            <select id="input_type" name="type" required class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                                <option value="movie" {{ old('type', $movie->type) === 'movie' ? 'selected' : '' }}>Film (Movie)</option>
                                <option value="series" {{ old('type', $movie->type) === 'series' ? 'selected' : '' }}>Serial (Series)</option>
                                <option value="anime" {{ old('type', $movie->type) === 'anime' ? 'selected' : '' }}>Anime</option>
                            </select>
                        </div>

                        <div>
                            <label for="input_release_year" class="block font-bold text-zinc-200 mb-1">
                                Tahun Rilis
                            </label>
                            <input type="number" 
                                   id="input_release_year" 
                                   name="release_year" 
                                   value="{{ old('release_year', $movie->release_year) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>

                        <div>
                            <label for="input_director" class="block font-bold text-zinc-200 mb-1">
                                Sutradara / Kreator
                            </label>
                            <input type="text" 
                                   id="input_director" 
                                   name="director" 
                                   value="{{ old('director', $movie->director) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <!-- Duration / Seasons / Episodes -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="input_runtime_minutes" class="block font-bold text-zinc-400 mb-1">
                                Durasi Menit (Film)
                            </label>
                            <input type="number" 
                                   id="input_runtime_minutes" 
                                   name="runtime_minutes" 
                                   value="{{ old('runtime_minutes', $movie->runtime_minutes) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>

                        <div>
                            <label for="input_total_seasons" class="block font-bold text-zinc-400 mb-1">
                                Total Season (Series)
                            </label>
                            <input type="number" 
                                   id="input_total_seasons" 
                                   name="total_seasons" 
                                   value="{{ old('total_seasons', $movie->total_seasons) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>

                        <div>
                            <label for="input_total_episodes" class="block font-bold text-zinc-400 mb-1">
                                Total Episode (Series)
                            </label>
                            <input type="number" 
                                   id="input_total_episodes" 
                                   name="total_episodes" 
                                   value="{{ old('total_episodes', $movie->total_episodes) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <!-- Poster & Backdrop URL -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="input_poster_url" class="block font-bold text-zinc-400 mb-1">
                                Poster Image URL
                            </label>
                            <input type="text" 
                                   id="input_poster_url" 
                                   name="poster_url" 
                                   value="{{ old('poster_url', $movie->poster_url) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>

                        <div>
                            <label for="input_backdrop_url" class="block font-bold text-zinc-400 mb-1">
                                Backdrop Banner URL
                            </label>
                            <input type="text" 
                                   id="input_backdrop_url" 
                                   name="backdrop_url" 
                                   value="{{ old('backdrop_url', $movie->backdrop_url) }}" 
                                   class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                        </div>
                    </div>

                    <!-- Synopsis -->
                    <div>
                        <label for="input_synopsis" class="block font-bold text-zinc-400 mb-1">
                            Sinopsis Resmi
                        </label>
                        <textarea id="input_synopsis" 
                                  name="synopsis" 
                                  rows="3" 
                                  class="w-full neo-input px-3.5 py-2 rounded-lg text-xs font-sans">{{ old('synopsis', $movie->synopsis) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION B: PENILAIAN RATING BINTANG INTERAKTIF & BERANIMASI -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#F59E0B] space-y-6">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                <x-lucide-star class="w-4 h-4 fill-amber-400" />
                <span>Penilaian Rating Bintang (Interaktif & Beranimasi)</span>
            </h2>

            <!-- Overall Rating -->
            <x-star-rating-input 
                name="rating_overall" 
                :value="old('rating_overall', $review->rating_overall)" 
                label="Rating Utama (Keseluruhan)" 
                size="lg" 
                :required="true" />

            <div class="pt-4 border-t border-slate-800">
                <h3 class="text-xs font-mono font-bold text-zinc-400 mb-3 uppercase">
                    Penilaian Aspek Spesifik (Opsional)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-star-rating-input name="rating_story" :value="old('rating_story', $review->rating_story ?? 0)" label="📖 Story & Writing" size="sm" />
                    <x-star-rating-input name="rating_acting" :value="old('rating_acting', $review->rating_acting ?? 0)" label="🎭 Acting & Characters" size="sm" />
                    <x-star-rating-input name="rating_visual" :value="old('rating_visual', $review->rating_visual ?? 0)" label="🎨 Visuals & Cinematography" size="sm" />
                    <x-star-rating-input name="rating_audio" :value="old('rating_audio', $review->rating_audio ?? 0)" label="🎵 Sound & Score / OST" size="sm" />
                </div>
            </div>
        </div>

        <!-- SECTION C: ULASAN & CATATAN PRIBADI -->
        <div class="bg-[#161622] border-2 border-slate-700 rounded-2xl p-6 sm:p-8 shadow-[6px_6px_0px_0px_#06B6D4] space-y-6">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                <x-lucide-file-text class="w-4 h-4" />
                <span>Ulasan & Detail Pengalaman Nonton</span>
            </h2>

            <div class="space-y-4 font-mono text-xs">
                <!-- Headline -->
                <div>
                    <label for="headline" class="block font-bold text-zinc-200 mb-1">
                        Headline / Judul Singkat Ulasan
                    </label>
                    <input type="text" 
                           id="headline" 
                           name="headline" 
                           value="{{ old('headline', $review->headline) }}" 
                           class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm font-sans font-medium">
                </div>

                <!-- Review Content (Markdown) -->
                <div>
                    <label for="review_content" class="block font-bold text-zinc-200 mb-1">
                        Isi Ulasan Lengkap (Mendukung Format Markdown)
                    </label>
                    <textarea id="review_content" 
                              name="review_content" 
                              rows="8" 
                              class="w-full neo-input p-3.5 rounded-lg text-sm font-sans leading-relaxed">{{ old('review_content', $review->review_content) }}</textarea>
                </div>

                <!-- Favorite Quote -->
                <div>
                    <label for="favorite_quote" class="block font-bold text-zinc-200 mb-1">
                        Kutipan Paling Berkesan (*Favorite Quote*)
                    </label>
                    <input type="text" 
                           id="favorite_quote" 
                           name="favorite_quote" 
                           value="{{ old('favorite_quote', $review->favorite_quote) }}" 
                           class="w-full neo-input px-3.5 py-2.5 rounded-lg text-sm italic font-serif">
                </div>

                <!-- Watch Platform, Date, Rewatch -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label for="watch_platform" class="block font-bold text-zinc-200 mb-1">
                            Tempat Menonton
                        </label>
                        <input type="text" 
                               id="watch_platform" 
                               name="watch_platform" 
                               value="{{ old('watch_platform', $review->watch_platform) }}" 
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <div>
                        <label for="watched_date" class="block font-bold text-zinc-200 mb-1">
                            Tanggal Selesai Nonton
                        </label>
                        <input type="date" 
                               id="watched_date" 
                               name="watched_date" 
                               value="{{ old('watched_date', $review->watched_date?->format('Y-m-d')) }}" 
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>

                    <div>
                        <label for="rewatch_count" class="block font-bold text-zinc-200 mb-1">
                            Jumlah Nonton Ulang (*Rewatch*)
                        </label>
                        <input type="number" 
                               id="rewatch_count" 
                               name="rewatch_count" 
                               value="{{ old('rewatch_count', $review->rewatch_count) }}" 
                               min="0"
                               class="w-full neo-input px-3 py-2 rounded-lg text-xs">
                    </div>
                </div>

                <!-- Checkboxes: Spoiler, Favorite, Published -->
                <div class="flex flex-wrap items-center gap-6 pt-3 border-t border-slate-800">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_spoiler" value="1" {{ old('is_spoiler', $review->is_spoiler) ? 'checked' : '' }} class="rounded border-zinc-700 bg-zinc-900 text-rose-500">
                        <span class="text-rose-300 font-bold">⚠️ Mengandung Spoiler</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_favorite" value="1" {{ old('is_favorite', $review->is_favorite) ? 'checked' : '' }} class="rounded border-zinc-700 bg-zinc-900 text-amber-500">
                        <span class="text-amber-300 font-bold">⭐ Sorot sebagai Favorit</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $review->is_published) ? 'checked' : '' }} class="rounded border-zinc-700 bg-zinc-900 text-emerald-500">
                        <span class="text-emerald-300 font-bold">🌐 Publikasikan</span>
                    </label>
                </div>

                <!-- Private Notes -->
                <div class="pt-2">
                    <label for="private_notes" class="block font-bold text-zinc-400 mb-1">
                        Catatan Rahasia (*Private Notes*)
                    </label>
                    <textarea id="private_notes" 
                              name="private_notes" 
                              rows="2" 
                              class="w-full neo-input px-3 py-2 rounded-lg text-xs">{{ old('private_notes', $review->private_notes) }}</textarea>
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
                    <span class="flex items-center gap-1.5 text-purple-400">
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
                <a href="{{ route('admin.reviews.index') }}" class="neo-btn px-5 py-3 rounded-xl bg-zinc-800 text-zinc-300 font-mono text-sm border border-slate-700">
                    Batal
                </a>

                <button type="submit" class="neo-btn px-8 py-3 rounded-xl bg-purple-500 hover:bg-purple-400 text-black text-sm font-bold font-mono shadow-[4px_4px_0px_0px_#fff]">
                    <x-lucide-check class="w-4 h-4 mr-2" />
                    <span>Perbarui Ulasan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
