<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['movieSeries.genres', 'user']);

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->whereHas('movieSeries', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_title', 'like', "%{$search}%");
            })->orWhere('headline', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->whereHas('movieSeries', fn ($q) => $q->where('type', $request->input('type')));
        }

        $reviews = $query->latest('watched_date')->latest('id')->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(Request $request)
    {
        $genres = Genre::orderBy('name')->get();

        // Check if prefilling from a watchlist item
        $watchlistPreFill = null;
        if ($request->filled('watchlist_id')) {
            $watchlistPreFill = Watchlist::with('movieSeries.genres')->find($request->input('watchlist_id'));
        }

        return view('admin.reviews.create', compact('genres', 'watchlistPreFill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Movie fields
            'title' => ['required', 'string', 'max:255'],
            'original_title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:movie,series,anime'],
            'tmdb_id' => ['nullable', 'integer'],
            'release_year' => ['nullable', 'integer', 'min:1880', 'max:2100'],
            'release_date' => ['nullable', 'date'],
            'synopsis' => ['nullable', 'string'],
            'poster_url' => ['nullable', 'string', 'max:500'],
            'backdrop_url' => ['nullable', 'string', 'max:500'],
            'director' => ['nullable', 'string', 'max:255'],
            'cast_members' => ['nullable'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['string'],
            'runtime_minutes' => ['nullable', 'integer'],
            'total_seasons' => ['nullable', 'integer'],
            'total_episodes' => ['nullable', 'integer'],
            'original_language' => ['nullable', 'string', 'max:10'],

            // Review fields
            'rating_overall' => ['required', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_story' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_visual' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_acting' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_audio' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'headline' => ['nullable', 'string', 'max:255'],
            'review_content' => ['nullable', 'string'],
            'favorite_quote' => ['nullable', 'string'],
            'is_spoiler' => ['boolean'],
            'is_favorite' => ['boolean'],
            'watch_platform' => ['nullable', 'string', 'max:100'],
            'watched_date' => ['nullable', 'date'],
            'rewatch_count' => ['nullable', 'integer', 'min:0'],
            'private_notes' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'watchlist_id' => ['nullable', 'integer'],
        ]);

        // Process Cast
        $cast = null;
        if (! empty($validated['cast_members'])) {
            $cast = is_array($validated['cast_members'])
                ? $validated['cast_members']
                : json_decode($validated['cast_members'], true);
        }

        // Find or create MovieSeries
        $movie = null;
        if (! empty($validated['tmdb_id'])) {
            $movie = MovieSeries::where('tmdb_id', $validated['tmdb_id'])->first();
        }

        if (! $movie) {
            $movie = MovieSeries::create([
                'tmdb_id' => $validated['tmdb_id'] ?? null,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'original_title' => $validated['original_title'] ?? null,
                'release_year' => $validated['release_year'] ?? ($validated['release_date'] ? (int) substr($validated['release_date'], 0, 4) : null),
                'release_date' => $validated['release_date'] ?? null,
                'synopsis' => $validated['synopsis'] ?? null,
                'poster_url' => $validated['poster_url'] ?? null,
                'backdrop_url' => $validated['backdrop_url'] ?? null,
                'director' => $validated['director'] ?? null,
                'cast_members' => $cast,
                'runtime_minutes' => $validated['runtime_minutes'] ?? null,
                'total_seasons' => $validated['total_seasons'] ?? null,
                'total_episodes' => $validated['total_episodes'] ?? null,
                'original_language' => $validated['original_language'] ?? null,
                'is_custom_entry' => empty($validated['tmdb_id']),
            ]);
        } else {
            // Update movie details if needed
            $movie->update([
                'title' => $validated['title'],
                'original_title' => $validated['original_title'] ?? $movie->original_title,
                'synopsis' => $validated['synopsis'] ?? $movie->synopsis,
                'poster_url' => $validated['poster_url'] ?? $movie->poster_url,
                'backdrop_url' => $validated['backdrop_url'] ?? $movie->backdrop_url,
                'director' => $validated['director'] ?? $movie->director,
                'cast_members' => $cast ?? $movie->cast_members,
                'runtime_minutes' => $validated['runtime_minutes'] ?? $movie->runtime_minutes,
                'total_seasons' => $validated['total_seasons'] ?? $movie->total_seasons,
                'total_episodes' => $validated['total_episodes'] ?? $movie->total_episodes,
            ]);
        }

        // Attach Genres
        if (! empty($validated['genres'])) {
            $genreIds = [];
            foreach ($validated['genres'] as $genreName) {
                if (! empty(trim($genreName))) {
                    $genre = Genre::firstOrCreate(
                        ['slug' => Str::slug($genreName)],
                        ['name' => trim($genreName)]
                    );
                    $genreIds[] = $genre->id;
                }
            }
            $movie->genres()->sync($genreIds);
        }

        // Create Review
        $review = Review::create([
            'user_id' => auth()->id(),
            'movie_series_id' => $movie->id,
            'rating_overall' => $validated['rating_overall'],
            'rating_story' => $validated['rating_story'] ?? null,
            'rating_visual' => $validated['rating_visual'] ?? null,
            'rating_acting' => $validated['rating_acting'] ?? null,
            'rating_audio' => $validated['rating_audio'] ?? null,
            'headline' => $validated['headline'] ?? null,
            'review_content' => $validated['review_content'] ?? null,
            'favorite_quote' => $validated['favorite_quote'] ?? null,
            'is_spoiler' => $request->boolean('is_spoiler'),
            'is_favorite' => $request->boolean('is_favorite'),
            'watch_platform' => $validated['watch_platform'] ?? null,
            'watched_date' => $validated['watched_date'] ?? now()->toDateString(),
            'rewatch_count' => $validated['rewatch_count'] ?? 0,
            'private_notes' => $validated['private_notes'] ?? null,
            'is_published' => $request->has('is_published') ? $request->boolean('is_published') : true,
        ]);

        // If converted from watchlist, mark as completed
        if (! empty($validated['watchlist_id'])) {
            $wl = Watchlist::find($validated['watchlist_id']);
            if ($wl) {
                $wl->update(['status' => 'completed']);
            }
        }

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan untuk "'.$movie->title.'" berhasil disimpan!');
    }

    public function edit(Review $review)
    {
        $review->load(['movieSeries.genres']);
        $genres = Genre::orderBy('name')->get();

        return view('admin.reviews.edit', compact('review', 'genres'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'original_title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:movie,series,anime'],
            'release_year' => ['nullable', 'integer'],
            'release_date' => ['nullable', 'date'],
            'synopsis' => ['nullable', 'string'],
            'poster_url' => ['nullable', 'string', 'max:500'],
            'backdrop_url' => ['nullable', 'string', 'max:500'],
            'director' => ['nullable', 'string', 'max:255'],
            'cast_members' => ['nullable'],
            'genres' => ['nullable', 'array'],
            'runtime_minutes' => ['nullable', 'integer'],
            'total_seasons' => ['nullable', 'integer'],
            'total_episodes' => ['nullable', 'integer'],

            'rating_overall' => ['required', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_story' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_visual' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_acting' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'rating_audio' => ['nullable', 'numeric', 'min:0.5', 'max:5.0'],
            'headline' => ['nullable', 'string', 'max:255'],
            'review_content' => ['nullable', 'string'],
            'favorite_quote' => ['nullable', 'string'],
            'watch_platform' => ['nullable', 'string', 'max:100'],
            'watched_date' => ['nullable', 'date'],
            'rewatch_count' => ['nullable', 'integer', 'min:0'],
            'private_notes' => ['nullable', 'string'],
        ]);

        $cast = null;
        if (! empty($validated['cast_members'])) {
            $cast = is_array($validated['cast_members'])
                ? $validated['cast_members']
                : json_decode($validated['cast_members'], true);
        }

        $movie = $review->movieSeries;
        $movie->update([
            'title' => $validated['title'],
            'original_title' => $validated['original_title'] ?? null,
            'type' => $validated['type'],
            'release_year' => $validated['release_year'] ?? ($validated['release_date'] ? (int) substr($validated['release_date'], 0, 4) : null),
            'release_date' => $validated['release_date'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
            'poster_url' => $validated['poster_url'] ?? null,
            'backdrop_url' => $validated['backdrop_url'] ?? null,
            'director' => $validated['director'] ?? null,
            'cast_members' => $cast ?? $movie->cast_members,
            'runtime_minutes' => $validated['runtime_minutes'] ?? null,
            'total_seasons' => $validated['total_seasons'] ?? null,
            'total_episodes' => $validated['total_episodes'] ?? null,
        ]);

        if (! empty($validated['genres'])) {
            $genreIds = [];
            foreach ($validated['genres'] as $genreName) {
                if (! empty(trim($genreName))) {
                    $genre = Genre::firstOrCreate(
                        ['slug' => Str::slug($genreName)],
                        ['name' => trim($genreName)]
                    );
                    $genreIds[] = $genre->id;
                }
            }
            $movie->genres()->sync($genreIds);
        }

        $review->update([
            'rating_overall' => $validated['rating_overall'],
            'rating_story' => $validated['rating_story'] ?? null,
            'rating_visual' => $validated['rating_visual'] ?? null,
            'rating_acting' => $validated['rating_acting'] ?? null,
            'rating_audio' => $validated['rating_audio'] ?? null,
            'headline' => $validated['headline'] ?? null,
            'review_content' => $validated['review_content'] ?? null,
            'favorite_quote' => $validated['favorite_quote'] ?? null,
            'is_spoiler' => $request->boolean('is_spoiler'),
            'is_favorite' => $request->boolean('is_favorite'),
            'watch_platform' => $validated['watch_platform'] ?? null,
            'watched_date' => $validated['watched_date'] ?? $review->watched_date,
            'rewatch_count' => $validated['rewatch_count'] ?? 0,
            'private_notes' => $validated['private_notes'] ?? null,
            'is_published' => $request->has('is_published') ? $request->boolean('is_published') : true,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan berhasil diperbarui!');
    }

    public function destroy(Review $review)
    {
        $title = $review->movieSeries->title;
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan untuk "'.$title.'" berhasil dihapus.');
    }
}
