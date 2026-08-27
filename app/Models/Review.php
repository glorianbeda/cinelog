<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'movie_series_id',
        'rating_overall',
        'rating_story',
        'rating_visual',
        'rating_acting',
        'rating_audio',
        'headline',
        'review_content',
        'favorite_quote',
        'is_spoiler',
        'is_favorite',
        'watch_platform',
        'watched_date',
        'rewatch_count',
        'private_notes',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'rating_overall' => 'float',
            'rating_story' => 'float',
            'rating_visual' => 'float',
            'rating_acting' => 'float',
            'rating_audio' => 'float',
            'is_spoiler' => 'boolean',
            'is_favorite' => 'boolean',
            'is_published' => 'boolean',
            'watched_date' => 'date',
            'rewatch_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movieSeries(): BelongsTo
    {
        return $this->belongsTo(MovieSeries::class, 'movie_series_id');
    }

    public function getRatingBadgeAttribute(): array
    {
        $rating = $this->rating_overall;

        if ($rating >= 9.5) {
            return [
                'label' => 'Masterpiece / Mahakarya',
                'badge_class' => 'bg-yellow-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#A855F7] animate-pulse',
                'stars' => 10.0,
            ];
        }

        if ($rating >= 8.5) {
            return [
                'label' => 'Great / High Praise',
                'badge_class' => 'bg-cyan-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        if ($rating >= 7.0) {
            return [
                'label' => 'Good / Recommended',
                'badge_class' => 'bg-emerald-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        if ($rating >= 5.0) {
            return [
                'label' => 'Decent / Mediocre',
                'badge_class' => 'bg-amber-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        if ($rating >= 3.0) {
            return [
                'label' => 'Bad / Disappointing',
                'badge_class' => 'bg-orange-500 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        return [
            'label' => 'Terrible / Unwatchable',
            'badge_class' => 'bg-rose-500 text-white border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
            'stars' => $rating,
        ];
    }

    public function getFormattedContentAttribute(): string
    {
        if (empty($this->review_content)) {
            return '';
        }

        return Str::markdown($this->review_content);
    }
}
