<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $owner = User::getOwner();

        $totalReviews = Review::where('is_published', true)->count();
        $totalMovies = MovieSeries::where('type', 'movie')->whereHas('reviews', fn($q) => $q->where('is_published', true))->count();
        $totalSeries = MovieSeries::whereIn('type', ['series', 'anime'])->whereHas('reviews', fn($q) => $q->where('is_published', true))->count();
        $avgRating = Review::where('is_published', true)->avg('rating_overall') ?? 0;
        
        $totalRuntimeMinutes = MovieSeries::whereHas('reviews', fn($q) => $q->where('is_published', true))->sum('runtime_minutes') ?? 0;
        $totalRuntimeHours = round($totalRuntimeMinutes / 60);

        // Rating distribution (0.5 to 5.0)
        $ratingDistribution = [];
        for ($i = 5.0; $i >= 1.0; $i -= 0.5) {
            $key = number_format($i, 1);
            $ratingDistribution[$key] = Review::where('is_published', true)
                ->where('rating_overall', $i)
                ->count();
        }

        // Top Genres
        $topGenres = Genre::withCount(['moviesSeries' => function ($q) {
            $q->whereHas('reviews', fn($r) => $r->where('is_published', true));
        }])->orderByDesc('movies_series_count')->take(10)->get();

        // Monthly breakdown for current year
        $currentYear = date('Y');
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = date('M', mktime(0, 0, 0, $m, 1));
            $count = Review::where('is_published', true)
                ->whereYear('watched_date', $currentYear)
                ->whereMonth('watched_date', $m)
                ->count();
            $monthlyData[$monthName] = $count;
        }

        // Top Directors
        $topDirectors = MovieSeries::whereHas('reviews', fn($q) => $q->where('is_published', true))
            ->whereNotNull('director')
            ->select('director', DB::raw('count(*) as total_count'))
            ->groupBy('director')
            ->orderByDesc('total_count')
            ->take(5)
            ->get();

        return view('stats.index', compact(
            'owner',
            'totalReviews',
            'totalMovies',
            'totalSeries',
            'avgRating',
            'totalRuntimeHours',
            'ratingDistribution',
            'topGenres',
            'monthlyData',
            'topDirectors'
        ));
    }
}
