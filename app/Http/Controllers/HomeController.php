<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $owner = User::getOwner();

        $featuredMasterpieces = Review::with(['movieSeries.genres', 'user'])
            ->where('is_published', true)
            ->where('rating_overall', '>=', 4.5)
            ->latest('watched_date')
            ->take(4)
            ->get();

        $latestReviews = Review::with(['movieSeries.genres', 'user'])
            ->where('is_published', true)
            ->latest('watched_date')
            ->latest('created_at')
            ->take(8)
            ->get();

        $currentlyWatching = Watchlist::with(['movieSeries.genres'])
            ->where('status', 'watching')
            ->latest('updated_at')
            ->take(4)
            ->get();

        // Compute Stats
        $totalReviews = Review::where('is_published', true)->count();
        $totalMovies = MovieSeries::where('type', 'movie')->whereHas('reviews', fn($q) => $q->where('is_published', true))->count();
        $totalSeries = MovieSeries::whereIn('type', ['series', 'anime'])->whereHas('reviews', fn($q) => $q->where('is_published', true))->count();
        $avgRating = Review::where('is_published', true)->avg('rating_overall') ?? 0;
        
        $totalRuntimeMinutes = MovieSeries::whereHas('reviews', fn($q) => $q->where('is_published', true))->sum('runtime_minutes') ?? 0;
        $totalRuntimeHours = round($totalRuntimeMinutes / 60);

        $genres = Genre::withCount(['moviesSeries' => function ($q) {
            $q->whereHas('reviews', fn($r) => $r->where('is_published', true));
        }])->orderByDesc('movies_series_count')->take(8)->get();

        return view('home', compact(
            'owner',
            'featuredMasterpieces',
            'latestReviews',
            'currentlyWatching',
            'totalReviews',
            'totalMovies',
            'totalSeries',
            'avgRating',
            'totalRuntimeHours',
            'genres'
        ));
    }
}
