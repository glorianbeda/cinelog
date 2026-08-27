@props([
    'review' => null,
    'movie' => null,
    'showReviewSnippet' => true
])

@php
    $m = $movie ?? $review?->movieSeries;
    $r = $review;
    if (!$m) return;
@endphp

<div class="group relative flex flex-col bg-[#161622] border-2 border-slate-700/80 rounded-xl overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,0.8)] hover:shadow-[6px_6px_0px_0px_#A855F7] hover:border-slate-500 hover:-translate-y-1.5 transition-all duration-200">
    <!-- Image & Overlay Container -->
    <a href="{{ route('reviews.show', $m->slug) }}" class="relative block aspect-[2/3] w-full overflow-hidden bg-zinc-900 border-b-2 border-slate-700/80">
        <img src="{{ $m->poster_image_url }}" 
             alt="{{ $m->title }}"
             loading="lazy"
             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>

        <!-- Top Badges -->
        <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between gap-1.5 pointer-events-none">
            <!-- Type Badge -->
            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase tracking-wider border-2 border-black
                {{ $m->type === 'movie' ? 'bg-cyan-400 text-black shadow-[2px_2px_0px_#000]' : 'bg-purple-400 text-black shadow-[2px_2px_0px_#000]' }}">
                {{ $m->type === 'movie' ? 'Film' : ($m->type === 'anime' ? 'Anime' : 'Series') }}
            </span>

            <!-- Rating Badge (if reviewed) -->
            @if($r)
                <div class="flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-amber-400 text-black border-2 border-black shadow-[2px_2px_0px_#000]">
                    <x-lucide-star class="w-3 h-3 fill-black" />
                    <span>{{ number_format($r->rating_overall, 1) }}</span>
                </div>
            @endif
        </div>

        <!-- Bottom Overlay Info inside poster -->
        <div class="absolute bottom-2.5 left-2.5 right-2.5 pointer-events-none">
            @if($m->release_year)
                <span class="text-[11px] font-mono font-bold text-zinc-300 bg-black/70 px-1.5 py-0.5 rounded border border-white/10">
                    {{ $m->release_year }}
                </span>
            @endif
            @if($m->runtime_minutes && $m->type === 'movie')
                <span class="text-[11px] font-mono text-zinc-300 bg-black/70 px-1.5 py-0.5 rounded border border-white/10 ml-1">
                    {{ $m->formatted_runtime }}
                </span>
            @endif
        </div>
    </a>

    <!-- Card Content -->
    <div class="flex flex-col flex-1 p-3.5 space-y-2">
        <a href="{{ route('reviews.show', $m->slug) }}" class="block">
            <h3 class="font-bold text-zinc-100 group-hover:text-amber-400 transition-colors line-clamp-1 leading-snug text-base" title="{{ $m->title }}">
                {{ $m->title }}
            </h3>
        </a>

        <!-- Director / Genres -->
        <div class="flex flex-wrap items-center gap-1 text-xs text-zinc-400">
            @if($m->director)
                <span class="inline-flex items-center gap-1 text-zinc-300 font-mono text-[11px]">
                    <x-lucide-clapperboard class="w-3 h-3 text-purple-400" />
                    <span class="line-clamp-1">{{ $m->director }}</span>
                </span>
            @endif
        </div>

        @if($m->genres->isNotEmpty())
            <div class="flex flex-wrap gap-1">
                @foreach($m->genres->take(2) as $genre)
                    <span class="px-1.5 py-0.5 bg-zinc-800 text-zinc-300 border border-zinc-700 rounded text-[10px] font-mono">
                        {{ $genre->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Review Headline Snippet -->
        @if($showReviewSnippet && $r && $r->headline)
            <div class="mt-auto pt-2 border-t border-slate-800 text-xs text-zinc-300 font-medium italic line-clamp-2">
                "{{ $r->headline }}"
            </div>
        @endif
    </div>
</div>
