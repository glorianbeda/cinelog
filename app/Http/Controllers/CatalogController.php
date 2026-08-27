<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $owner = User::getOwner();

        $query = Review::with(['movieSeries.genres', 'user'])
            ->where('is_published', true);

        // Search
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('headline', 'like', "%{$search}%")
                  ->orWhere('review_content', 'like', "%{$search}%")
                  ->orWhereHas('movieSeries', function ($mq) use ($search) {
                      $mq->where('title', 'like', "%{$search}%")
                         ->orWhere('original_title', 'like', "%{$search}%")
                         ->orWhere('director', 'like', "%{$search}%")
                         ->orWhere('synopsis', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Type
        if ($request->filled('type') && in_array($request->input('type'), ['movie', 'series', 'anime'])) {
            $query->whereHas('movieSeries', function ($q) use ($request) {
                $q->where('type', $request->input('type'));
            });
        }

        // Filter Genre
        if ($request->filled('genre')) {
            $genreSlug = $request->input('genre');
            $query->whereHas('movieSeries.genres', function ($q) use ($genreSlug) {
                $q->where('slug', $genreSlug);
            });
        }

        // Filter Min Rating
        if ($request->filled('min_rating')) {
            $minRating = (float) $request->input('min_rating');
            $query->where('rating_overall', '>=', $minRating);
        }

        // Filter Year
        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            $query->whereHas('movieSeries', function ($q) use ($year) {
                $q->where('release_year', $year);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'rating_desc':
                $query->orderByDesc('rating_overall')->latest('watched_date');
                break;
            case 'rating_asc':
                $query->orderBy('rating_overall')->latest('watched_date');
                break;
            case 'year_desc':
                $query->join('movies_series', 'reviews.movie_series_id', '=', 'movies_series.id')
                      ->orderByDesc('movies_series.release_year')
                      ->select('reviews.*');
                break;
            case 'title_asc':
                $query->join('movies_series', 'reviews.movie_series_id', '=', 'movies_series.id')
                      ->orderBy('movies_series.title')
                      ->select('reviews.*');
                break;
            case 'latest':
            default:
                $query->latest('watched_date')->latest('created_at');
                break;
        }

        $reviews = $query->paginate(12)->withQueryString();

        $genres = Genre::whereHas('moviesSeries.reviews', fn($q) => $q->where('is_published', true))
            ->orderBy('name')
            ->get();

        $years = MovieSeries::whereHas('reviews', fn($q) => $q->where('is_published', true))
            ->whereNotNull('release_year')
            ->distinct()
            ->orderByDesc('release_year')
            ->pluck('release_year');

        return view('catalog.index', compact('owner', 'reviews', 'genres', 'years'));
    }
}
