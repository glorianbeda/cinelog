<?php

namespace App\Http\Controllers;

use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;

class ReviewDetailController extends Controller
{
    public function show(string $slug)
    {
        $owner = User::getOwner();

        $movie = MovieSeries::with(['genres', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $review = $movie->reviews()->where('is_published', true)->latest()->first();

        if (! $review && ! auth()->check()) {
            abort(404);
        }

        // Related reviews
        $genreIds = $movie->genres->pluck('id');
        $relatedReviews = Review::with(['movieSeries.genres'])
            ->where('is_published', true)
            ->where('movie_series_id', '!=', $movie->id)
            ->whereHas('movieSeries.genres', function ($q) use ($genreIds) {
                $q->whereIn('genres.id', $genreIds);
            })
            ->latest('watched_date')
            ->take(4)
            ->get();

        return view('reviews.show', compact('owner', 'movie', 'review', 'relatedReviews'));
    }
}
