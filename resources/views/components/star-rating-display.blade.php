@props([
    'rating' => 0,
    'size' => 'md',
    'showScore' => true,
])

@php
    $r = (float) $rating;
    $starSizeClass = match($size) {
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-6 h-6',
        default => 'w-4 h-4'
    };
@endphp

<div class="inline-flex items-center gap-1.5 select-none">
    <div class="flex items-center gap-0.5">
        @for ($i = 1; $i <= 5; $i++)
            @if ($r >= $i)
                <!-- Full Star -->
                <svg class="{{ $starSizeClass }} text-amber-400 drop-shadow-[0_0_6px_rgba(245,158,11,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            @elseif ($r >= $i - 0.5)
                <!-- Half Star -->
                <div class="relative {{ $starSizeClass }}">
                    <svg class="absolute inset-0 {{ $starSizeClass }} text-zinc-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <div class="absolute inset-0 w-1/2 overflow-hidden">
                        <svg class="{{ $starSizeClass }} text-amber-400 drop-shadow-[0_0_6px_rgba(245,158,11,0.5)]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>
            @else
                <!-- Empty Star -->
                <svg class="{{ $starSizeClass }} text-zinc-700/80" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            @endif
        @endfor
    </div>

    @if($showScore)
        <span class="font-mono font-bold text-amber-400 {{ $size === 'lg' ? 'text-lg' : 'text-xs' }}">
            {{ number_format($r, 1) }}
        </span>
    @endif
</div>
