<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    protected $fillable = [
        'tmdb_genre_id',
        'name',
        'slug',
    ];

    public function moviesSeries(): BelongsToMany
    {
        return $this->belongsToMany(MovieSeries::class, 'movie_genres');
    }
}
