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

        if ($rating >= 4.8) {
            return [
                'label' => 'Masterpiece',
                'badge_class' => 'bg-yellow-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#A855F7]',
                'stars' => 5.0,
            ];
        }

        if ($rating >= 4.0) {
            return [
                'label' => 'Great / Recommended',
                'badge_class' => 'bg-cyan-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        if ($rating >= 3.0) {
            return [
                'label' => 'Good / Worth Watching',
                'badge_class' => 'bg-emerald-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        if ($rating >= 2.0) {
            return [
                'label' => 'Mediocre / Decent',
                'badge_class' => 'bg-amber-400 text-black border-2 border-white/40 shadow-[2px_2px_0px_#fff]',
                'stars' => $rating,
            ];
        }

        return [
            'label' => 'Terrible / Disappointing',
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
