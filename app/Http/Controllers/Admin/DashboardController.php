<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $owner = auth()->user();

        $totalReviews = Review::count();
        $totalMovies = MovieSeries::where('type', 'movie')->count();
        $totalSeries = MovieSeries::whereIn('type', ['series', 'anime'])->count();
        $totalWatchlist = Watchlist::whereIn('status', ['plan_to_watch', 'watching'])->count();
        $avgRating = Review::avg('rating_overall') ?? 0;

        $recentReviews = Review::with('movieSeries.genres')
            ->latest()
            ->take(5)
            ->get();

        $activeWatchlist = Watchlist::with('movieSeries')
            ->where('status', 'watching')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Monthly stats for chart
        $currentYear = date('Y');
        $chartLabels = [];
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $chartData[] = Review::whereYear('watched_date', $currentYear)
                ->whereMonth('watched_date', $m)
                ->count();
        }

        // Genre breakdown for donut chart
        $genreStats = Genre::withCount('moviesSeries')
            ->having('movies_series_count', '>', 0)
            ->orderByDesc('movies_series_count')
            ->take(6)
            ->get();

        $genreLabels = $genreStats->pluck('name')->toArray();
        $genreData = $genreStats->pluck('movies_series_count')->toArray();

        return view('admin.dashboard', compact(
            'owner',
            'totalReviews',
            'totalMovies',
            'totalSeries',
            'totalWatchlist',
            'avgRating',
            'recentReviews',
            'activeWatchlist',
            'chartLabels',
            'chartData',
            'genreLabels',
            'genreData'
        ));
    }
}
