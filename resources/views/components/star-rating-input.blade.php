@props([
    'name' => 'rating_overall',
    'value' => 0,
    'label' => 'Rating Keseluruhan',
    'size' => 'lg', // 'sm', 'md', 'lg'
    'required' => false
])

@php
    $starSizeClass = match($size) {
        'sm' => 'w-5 h-5',
        'md' => 'w-7 h-7',
        'lg' => 'w-9 h-9',
        default => 'w-9 h-9'
    };
@endphp

<div x-data="starRating({{ (float) ($value ?? 0) }}, '{{ $name }}')" class="space-y-2">
    @if($label)
        <div class="flex items-center justify-between">
            <label class="block text-sm font-bold text-zinc-200 uppercase tracking-wider font-mono">
                {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <span x-text="ratingLabel" 
                  :class="badgeColorClass" 
                  class="px-2.5 py-0.5 rounded text-xs font-mono font-bold tracking-wide transition-all duration-200">
            </span>
        </div>
    @endif

    <input type="hidden" name="{{ $name }}" :value="rating">

    <div class="flex items-center gap-1.5 p-3 bg-zinc-900/80 border-2 border-zinc-700/80 rounded-lg select-none">
        <div class="flex items-center gap-1" @mouseleave="clearHover()">
            @for ($i = 1; $i <= 5; $i++)
                <div class="relative cursor-pointer transition-transform duration-150 hover:scale-115"
                     :class="{ 'animate-pop': isPopping && (rating >= {{ $i }} - 0.5) }">
                    
                    <!-- Left Half Hitbox (0.5) -->
                    <div class="absolute inset-y-0 left-0 w-1/2 z-10"
                         @mouseenter="setHover({{ $i }}, true)"
                         @click="selectRating({{ $i }}, true)">
                    </div>

                    <!-- Right Half Hitbox (1.0) -->
                    <div class="absolute inset-y-0 right-0 w-1/2 z-10"
                         @mouseenter="setHover({{ $i }}, false)"
                         @click="selectRating({{ $i }}, false)">
                    </div>

                    <!-- Visual Star SVG -->
                    <div class="transition-all duration-150 pointer-events-none">
                        <template x-if="getStarState({{ $i }}) === 'full'">
                            <svg class="{{ $starSizeClass }} text-amber-400 drop-shadow-[0_0_8px_rgba(245,158,11,0.6)]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </template>

                        <template x-if="getStarState({{ $i }}) === 'half'">
                            <div class="relative {{ $starSizeClass }}">
                                <!-- Background empty star -->
                                <svg class="absolute inset-0 {{ $starSizeClass }} text-zinc-700" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <!-- Half filled star -->
                                <div class="absolute inset-0 w-1/2 overflow-hidden">
                                    <svg class="{{ $starSizeClass }} text-amber-400 drop-shadow-[0_0_8px_rgba(245,158,11,0.6)]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </template>

                        <template x-if="getStarState({{ $i }}) === 'empty'">
                            <svg class="{{ $starSizeClass }} text-zinc-700/80 hover:text-zinc-600 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </template>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Numeric Display & Reset Button -->
        <div class="ml-auto flex items-center gap-2">
            <span class="font-mono text-sm font-bold text-amber-400 bg-black/40 px-2 py-0.5 rounded border border-amber-500/20" 
                  x-text="(currentDisplayRating).toFixed(1) + ' ★'">
            </span>
            <button type="button" 
                    @click="reset()" 
                    title="Reset rating"
                    class="p-1 text-zinc-500 hover:text-rose-400 hover:bg-zinc-800 rounded transition-colors">
                <x-lucide-rotate-ccw class="w-4 h-4" />
            </button>
        </div>
    </div>
</div>
