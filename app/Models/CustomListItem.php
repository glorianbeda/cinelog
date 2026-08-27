<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomListItem extends Model
{
    protected $fillable = [
        'custom_list_id',
        'movie_series_id',
        'order_position',
        'item_note',
    ];

    public function customList(): BelongsTo
    {
        return $this->belongsTo(CustomList::class);
    }

    public function movieSeries(): BelongsTo
    {
        return $this->belongsTo(MovieSeries::class, 'movie_series_id');
    }
}
