<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class MovieSeries extends Model
{
    protected $table = 'movies_series';

    protected $fillable = [
        'tmdb_id',
        'type',
        'title',
        'original_title',
        'slug',
        'release_year',
        'release_date',
        'synopsis',
        'poster_url',
        'backdrop_url',
        'director',
        'cast_members',
        'runtime_minutes',
        'total_seasons',
        'total_episodes',
        'original_language',
        'is_custom_entry',
    ];

    protected function casts(): array
    {
        return [
            'cast_members' => 'array',
            'release_date' => 'date',
            'is_custom_entry' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($movie) {
            if (empty($movie->slug)) {
                $movie->slug = Str::slug($movie->title) . '-' . ($movie->release_year ?? time());
                // ensure uniqueness
                $originalSlug = $movie->slug;
                $count = 1;
                while (static::where('slug', $movie->slug)->exists()) {
                    $movie->slug = "{$originalSlug}-{$count}";
                    $count++;
                }
            }
        });
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genres');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class)->latestOfMany();
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function getPosterImageUrlAttribute(): string
    {
        if (empty($this->poster_url)) {
            return 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=600&auto=format&fit=crop';
        }

        if (Str::startsWith($this->poster_url, ['http://', 'https://', '/'])) {
            return $this->poster_url;
        }

        return 'https://image.tmdb.org/t/p/w500' . $this->poster_url;
    }

    public function getBackdropImageUrlAttribute(): string
    {
        if (empty($this->backdrop_url)) {
            return $this->poster_image_url;
        }

        if (Str::startsWith($this->backdrop_url, ['http://', 'https://', '/'])) {
            return $this->backdrop_url;
        }

        return 'https://image.tmdb.org/t/p/w1280' . $this->backdrop_url;
    }

    public function getFormattedRuntimeAttribute(): string
    {
        if ($this->type === 'movie' && $this->runtime_minutes) {
            $hours = floor($this->runtime_minutes / 60);
            $mins = $this->runtime_minutes % 60;
            return $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
        }

        if ($this->type !== 'movie') {
            $parts = [];
            if ($this->total_seasons) {
                $parts[] = "{$this->total_seasons} Season" . ($this->total_seasons > 1 ? 's' : '');
            }
            if ($this->total_episodes) {
                $parts[] = "{$this->total_episodes} Eps";
            }
            return implode(' • ', $parts);
        }

        return '-';
    }
}
