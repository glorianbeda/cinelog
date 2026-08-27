<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    protected $fillable = [
        'user_id',
        'movie_series_id',
        'status',
        'current_season',
        'current_episode',
        'priority',
        'watch_platform',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movieSeries(): BelongsTo
    {
        return $this->belongsTo(MovieSeries::class, 'movie_series_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'watching' => ['label' => 'Sedang Menonton', 'class' => 'bg-emerald-400 text-black border-2 border-slate-700 shadow-[2px_2px_0px_#10B981]'],
            'plan_to_watch' => ['label' => 'Rencana Nonton', 'class' => 'bg-cyan-400 text-black border-2 border-slate-700 shadow-[2px_2px_0px_#06B6D4]'],
            'on_hold' => ['label' => 'Tertunda / On Hold', 'class' => 'bg-amber-400 text-black border-2 border-slate-700 shadow-[2px_2px_0px_#F59E0B]'],
            'dropped' => ['label' => 'Dropped', 'class' => 'bg-rose-500 text-white border-2 border-slate-700 shadow-[2px_2px_0px_#EF4444]'],
            'completed' => ['label' => 'Selesai', 'class' => 'bg-purple-400 text-black border-2 border-slate-700 shadow-[2px_2px_0px_#A855F7]'],
            default => ['label' => $this->status, 'class' => 'bg-zinc-700 text-white border-2 border-slate-700'],
        };
    }
}
