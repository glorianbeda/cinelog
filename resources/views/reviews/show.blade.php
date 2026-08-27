@extends('layouts.app')

@section('title', $movie->title . ' (' . ($movie->release_year ?? 'Ulasan') . ') — CineLog')

@section('content')
<div class="space-y-12">
    <!-- CINEMATIC BACKDROP BANNER -->
    <div class="relative w-full h-[320px] sm:h-[420px] lg:h-[500px] overflow-hidden bg-zinc-950 border-b-2 border-slate-700">
        <img src="{{ $movie->backdrop_image_url }}" 
             alt="{{ $movie->title }}"
             class="w-full h-full object-cover opacity-35 scale-105 filter blur-[1px]">
        
        <!-- Multi-layer Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0D0D12] via-[#0D0D12]/70 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0D0D12] via-[#0D0D12]/60 to-transparent"></div>

        <!-- Floating Quick Badges -->
        <div class="absolute bottom-6 left-4 sm:left-8 lg:left-12 flex flex-wrap items-center gap-2 font-mono text-xs z-10">
            <span class="px-2.5 py-1 rounded bg-black/80 text-cyan-400 border border-cyan-500/40 font-bold">
                {{ $movie->type === 'movie' ? 'Film Layar Lebar' : ($movie->type === 'anime' ? 'Serial Anime' : 'Serial Televisi') }}
            </span>
            @if($movie->release_year)
                <span class="px-2.5 py-1 rounded bg-black/80 text-zinc-300 border border-white/20 font-bold">
                    {{ $movie->release_year }}
                </span>
            @endif
            @if($movie->formatted_runtime !== '-')
                <span class="px-2.5 py-1 rounded bg-black/80 text-zinc-300 border border-white/20 font-bold">
                    {{ $movie->formatted_runtime }}
                </span>
            @endif
        </div>
    </div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 sm:-mt-36 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            <!-- LEFT COLUMN (Poster & Metadata Box) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Poster Card with Neubrutalism Shadow -->
                <div class="bg-[#161622] border-3 border-slate-600 rounded-2xl p-2 shadow-[8px_8px_0px_0px_#A855F7] overflow-hidden">
                    <img src="{{ $movie->poster_image_url }}" 
                         alt="{{ $movie->title }}"
                         class="w-full aspect-[2/3] object-cover rounded-xl border border-slate-700">
                </div>

                <!-- Info Box -->
                <div class="bg-[#14141E] border-2 border-slate-700 rounded-2xl p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] space-y-4 text-xs font-mono">
                    <h3 class="font-bold text-amber-400 uppercase tracking-wider text-xs border-b border-slate-800 pb-2 flex items-center gap-2">
                        <x-lucide-info class="w-4 h-4" />
                        <span>Informasi & Metadata</span>
                    </h3>

                    <!-- Director -->
                    @if($movie->director)
                        <div>
                            <span class="text-zinc-400 block mb-0.5">Sutradara / Kreator:</span>
                            <span class="font-bold text-white text-sm">{{ $movie->director }}</span>
                        </div>
                    @endif

                    <!-- Genres -->
                    @if($movie->genres->isNotEmpty())
                        <div>
                            <span class="text-zinc-400 block mb-1">Genre:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($movie->genres as $genre)
                                    <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}" 
                                       class="px-2 py-0.5 bg-zinc-800 hover:bg-purple-500/20 text-purple-300 border border-slate-700 hover:border-purple-500 rounded text-[11px] font-bold transition-colors">
                                        {{ $genre->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Watch Platform -->
                    @if($review && $review->watch_platform)
                        <div>
                            <span class="text-zinc-400 block mb-0.5">Tempat Menonton:</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 rounded font-bold">
                                <x-lucide-tv class="w-3.5 h-3.5" />
                                <span>{{ $review->watch_platform }}</span>
                            </span>
                        </div>
                    @endif

                    <!-- Logged Date -->
                    @if($review && $review->watched_date)
                        <div>
                            <span class="text-zinc-400 block mb-0.5">Tanggal Nonton:</span>
                            <span class="text-zinc-200 font-bold">{{ $review->watched_date->isoFormat('D MMMM Y') }}</span>
                        </div>
                    @endif

                    <!-- Cast Members Preview -->
                    @if(!empty($movie->cast_members) && is_array($movie->cast_members))
                        <div class="pt-2 border-t border-slate-800">
                            <span class="text-zinc-400 block mb-2 font-bold uppercase tracking-wider text-[11px]">Pemeran Utama:</span>
                            <div class="space-y-2">
                                @foreach(array_slice($movie->cast_members, 0, 5) as $actor)
                                    <div class="flex items-center gap-2">
                                        @if(!empty($actor['profile_url']))
                                            <img src="{{ $actor['profile_url'] }}" alt="{{ $actor['name'] }}" class="w-6 h-6 rounded-full object-cover border border-slate-700">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] text-zinc-400 border border-slate-700">
                                                <x-lucide-user class="w-3.5 h-3.5" />
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-white truncate text-[11px]">{{ $actor['name'] }}</p>
                                            @if(!empty($actor['character']))
                                                <p class="text-zinc-400 truncate text-[10px]">as {{ $actor['character'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @auth
                    <!-- Admin Edit Action Button -->
                    <div class="pt-2">
                        <a href="{{ route('admin.reviews.edit', $review) }}" 
                           class="w-full neo-btn py-2.5 px-4 rounded-xl bg-purple-500 hover:bg-purple-400 text-black text-xs font-mono font-bold shadow-[4px_4px_0px_0px_#fff]">
                            <x-lucide-edit-3 class="w-4 h-4 mr-1.5" />
                            <span>Edit Ulasan Ini</span>
                        </a>
                    </div>
                @endauth
            </div>

            <!-- RIGHT COLUMN (Review Content & Scores) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Title Header -->
                <div class="space-y-2">
                    <h1 class="text-3xl sm:text-5xl font-black font-mono tracking-tight text-white leading-tight">
                        {{ $movie->title }}
                    </h1>
                    @if($movie->original_title && $movie->original_title !== $movie->title)
                        <p class="text-sm font-mono text-zinc-400 italic">
                            Original Title: {{ $movie->original_title }}
                        </p>
                    @endif
                </div>

                <!-- OVERALL RATING SHOWCASE BOX -->
                @if($review)
                    @php $badge = $review->rating_badge; @endphp
                    <div class="bg-[#181826] border-3 border-slate-600 rounded-2xl p-6 shadow-[6px_6px_0px_0px_#F59E0B] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 flex items-center gap-1.5">
                                <x-lucide-award class="w-4 h-4 text-amber-400" />
                                <span>Penilaian Kurator</span>
                            </span>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-4xl sm:text-5xl font-black font-mono text-amber-400 drop-shadow-[0_0_12px_rgba(245,158,11,0.5)]">
                                    {{ number_format($review->rating_overall, 1) }}
                                </span>
                                <div class="space-y-1">
                                    <x-star-rating-display :rating="$review->rating_overall" size="lg" :showScore="false" />
                                    <span class="text-xs font-mono text-zinc-400 block">dari skala 5.0 bintang</span>
                                </div>
                            </div>
                        </div>

                        <!-- Predicate Badge -->
                        <div class="px-4 py-2 rounded-xl text-xs font-mono font-bold {{ $badge['badge_class'] }}">
                            {{ $badge['label'] }}
                        </div>
                    </div>

                    <!-- SUB-CRITERIA RATING BREAKDOWN (If Available) -->
                    @if($review->rating_story || $review->rating_visual || $review->rating_acting || $review->rating_audio)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @if($review->rating_story)
                                <div class="bg-[#14141E] border-2 border-slate-700 p-3 rounded-xl shadow-[2px_2px_0px_#000] space-y-1">
                                    <span class="text-[11px] font-mono text-zinc-400 block font-bold">📖 Story / Naskah</span>
                                    <x-star-rating-display :rating="$review->rating_story" size="sm" />
                                </div>
                            @endif
                            @if($review->rating_acting)
                                <div class="bg-[#14141E] border-2 border-slate-700 p-3 rounded-xl shadow-[2px_2px_0px_#000] space-y-1">
                                    <span class="text-[11px] font-mono text-zinc-400 block font-bold">🎭 Akting & Karakter</span>
                                    <x-star-rating-display :rating="$review->rating_acting" size="sm" />
                                </div>
                            @endif
                            @if($review->rating_visual)
                                <div class="bg-[#14141E] border-2 border-slate-700 p-3 rounded-xl shadow-[2px_2px_0px_#000] space-y-1">
                                    <span class="text-[11px] font-mono text-zinc-400 block font-bold">🎨 Sinematografi</span>
                                    <x-star-rating-display :rating="$review->rating_visual" size="sm" />
                                </div>
                            @endif
                            @if($review->rating_audio)
                                <div class="bg-[#14141E] border-2 border-slate-700 p-3 rounded-xl shadow-[2px_2px_0px_#000] space-y-1">
                                    <span class="text-[11px] font-mono text-zinc-400 block font-bold">🎵 Musik & Audio</span>
                                    <x-star-rating-display :rating="$review->rating_audio" size="sm" />
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- REVIEW HEADLINE -->
                    @if($review->headline)
                        <div class="p-4 bg-purple-950/30 border-l-4 border-purple-500 rounded-r-xl text-purple-200 font-mono font-bold text-base sm:text-lg">
                            "{{ $review->headline }}"
                        </div>
                    @endif

                    <!-- FAVORITE QUOTE -->
                    @if($review->favorite_quote)
                        <div class="bg-[#161622] border-2 border-slate-700 p-5 rounded-2xl shadow-[4px_4px_0px_0px_#06B6D4] space-y-2">
                            <div class="flex items-center gap-2 text-cyan-400 font-mono text-xs font-bold uppercase">
                                <x-lucide-quote class="w-4 h-4" />
                                <span>Kutipan Paling Berkesan</span>
                            </div>
                            <blockquote class="italic text-zinc-200 text-sm sm:text-base font-serif pl-3 border-l-2 border-cyan-400">
                                "{{ $review->favorite_quote }}"
                            </blockquote>
                        </div>
                    @endif

                    <!-- REVIEW CONTENT BODY (With Spoiler Protection) -->
                    <div x-data="{ showSpoiler: {{ $review->is_spoiler ? 'false' : 'true' }} }" class="space-y-4">
                        @if($review->is_spoiler)
                            <div x-show="!showSpoiler" class="p-5 bg-rose-950/40 border-2 border-rose-500 rounded-2xl text-center space-y-3 shadow-[4px_4px_0px_0px_#EF4444]">
                                <x-lucide-alert-triangle class="w-8 h-8 mx-auto text-rose-400" />
                                <div>
                                    <h4 class="font-mono font-bold text-white text-sm">Peringatan Spoiler!</h4>
                                    <p class="text-xs font-mono text-rose-200 mt-1">Ulasan ini memuat rincian penting jalan cerita atau akhir film.</p>
                                </div>
                                <button type="button" 
                                        @click="showSpoiler = true" 
                                        class="neo-btn px-4 py-2 rounded-lg bg-rose-500 hover:bg-rose-400 text-white text-xs font-mono font-bold">
                                    Buka & Tampilkan Ulasan Lengkap
                                </button>
                            </div>
                        @endif

                        <div x-show="showSpoiler" class="prose prose-invert max-w-none bg-[#14141E] border-2 border-slate-700 p-6 sm:p-8 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] text-zinc-200 leading-relaxed font-sans text-base">
                            <h3 class="font-mono font-bold text-amber-400 uppercase tracking-wider text-sm border-b border-slate-800 pb-3 mb-4 flex items-center gap-2">
                                <x-lucide-file-text class="w-4 h-4" />
                                <span>Ulasan Mendalam</span>
                            </h3>

                            {!! $review->formatted_content !!}

                            <!-- Review Signature / Author Footnote -->
                            <div class="mt-8 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-mono text-zinc-400">
                                <span>Diulas oleh <strong class="text-amber-400">{{ $review->user->name ?? $owner->name }}</strong></span>
                                @if($review->rewatch_count > 0)
                                    <span class="px-2 py-0.5 bg-zinc-800 text-zinc-300 rounded border border-slate-700">
                                        Nonton ulang ke-{{ $review->rewatch_count + 1 }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- MOVIE SYNOPSIS (From TMDB / Metadata) -->
                @if($movie->synopsis)
                    <div class="bg-[#12121A] border-2 border-slate-800 p-6 rounded-2xl space-y-2">
                        <h3 class="font-mono font-bold text-zinc-400 uppercase tracking-wider text-xs flex items-center gap-2">
                            <x-lucide-align-left class="w-4 h-4 text-purple-400" />
                            <span>Sinopsis Resmi</span>
                        </h3>
                        <p class="text-sm text-zinc-300 leading-relaxed font-sans">
                            {{ $movie->synopsis }}
                        </p>
                    </div>
                @endif

                <!-- SOCIAL SHARE BUTTONS -->
                <div class="flex items-center gap-3 pt-4 border-t border-slate-800 font-mono text-xs">
                    <span class="text-zinc-400 font-bold">Bagikan Ulasan:</span>

                    <!-- Twitter / X -->
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode('Ulasan ' . $movie->title . ' (' . number_format($review?->rating_overall ?? 5, 1) . '★) oleh ' . ($owner->name ?? 'CineLog') . ': ' . url()->current()) }}" 
                       target="_blank" 
                       class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-black text-white border border-slate-700 hover:border-white text-xs font-bold transition-all">
                        Twitter / X
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode('Baca ulasan ' . $movie->title . ': ' . url()->current()) }}" 
                       target="_blank" 
                       class="px-3 py-1.5 rounded-lg bg-emerald-950 hover:bg-emerald-900 text-emerald-300 border border-emerald-700 text-xs font-bold transition-all">
                        WhatsApp
                    </a>

                    <!-- Copy Link -->
                    <button type="button" 
                            onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan ulasan berhasil disalin ke clipboard!');" 
                            class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 border border-slate-700 text-xs font-bold transition-all">
                        Salin Tautan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RELATED REVIEWS SECTION -->
    @if($relatedReviews->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t-2 border-slate-800 space-y-6">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-cyan-500/15 text-cyan-400 border border-cyan-500/40">
                    <x-lucide-film class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-black font-mono tracking-tight text-white">
                        ULASAN <span class="text-cyan-400">SERUPA</span>
                    </h2>
                    <p class="text-xs text-zinc-400 font-mono">
                        Rekomendasi judul lain dengan genre serupa
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedReviews as $rel)
                    <x-movie-card :review="$rel" :showReviewSnippet="false" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
