<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = Watchlist::with(['movieSeries.genres', 'movieSeries.reviews'])->latest('updated_at');

        if ($status === 'needs_review') {
            $query->where('status', 'completed')
                ->whereDoesntHave('movieSeries.reviews');
        } elseif ($status !== 'all' && in_array($status, ['plan_to_watch', 'watching', 'on_hold', 'dropped', 'completed'])) {
            $query->where('status', $status);
        }

        $items = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Watchlist::count(),
            'watching' => Watchlist::where('status', 'watching')->count(),
            'plan_to_watch' => Watchlist::where('status', 'plan_to_watch')->count(),
            'completed' => Watchlist::where('status', 'completed')->count(),
            'needs_review' => Watchlist::where('status', 'completed')->whereDoesntHave('movieSeries.reviews')->count(),
            'on_hold' => Watchlist::where('status', 'on_hold')->count(),
            'dropped' => Watchlist::where('status', 'dropped')->count(),
        ];

        return view('admin.watchlist.index', compact('items', 'counts', 'status'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();

        return view('admin.watchlist.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:movie,series,anime'],
            'tmdb_id' => ['nullable', 'integer'],
            'release_year' => ['nullable', 'integer'],
            'synopsis' => ['nullable', 'string'],
            'poster_url' => ['nullable', 'string', 'max:500'],
            'backdrop_url' => ['nullable', 'string', 'max:500'],
            'director' => ['nullable', 'string', 'max:255'],
            'runtime_minutes' => ['nullable', 'integer'],
            'total_seasons' => ['nullable', 'integer'],
            'total_episodes' => ['nullable', 'integer'],
            'genres' => ['nullable', 'array'],

            'status' => ['required', 'in:plan_to_watch,watching,on_hold,dropped,completed'],
            'current_season' => ['nullable', 'integer', 'min:1'],
            'current_episode' => ['nullable', 'integer', 'min:0'],
            'priority' => ['required', 'in:high,medium,low'],
            'watch_platform' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

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
                'release_year' => $validated['release_year'] ?? null,
                'synopsis' => $validated['synopsis'] ?? null,
                'poster_url' => $validated['poster_url'] ?? null,
                'backdrop_url' => $validated['backdrop_url'] ?? null,
                'director' => $validated['director'] ?? null,
                'runtime_minutes' => $validated['runtime_minutes'] ?? null,
                'total_seasons' => $validated['total_seasons'] ?? null,
                'total_episodes' => $validated['total_episodes'] ?? null,
                'is_custom_entry' => empty($validated['tmdb_id']),
            ]);
        }

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

        Watchlist::updateOrCreate(
            ['user_id' => auth()->id(), 'movie_series_id' => $movie->id],
            [
                'status' => $validated['status'],
                'current_season' => $validated['current_season'] ?? 1,
                'current_episode' => $validated['current_episode'] ?? 0,
                'priority' => $validated['priority'],
                'watch_platform' => $validated['watch_platform'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('admin.watchlist.index')->with('success', 'Berhasil menambahkan "'.$movie->title.'" ke antrean tontonan.');
    }

    public function updateProgress(Request $request, Watchlist $watchlist)
    {
        $direction = $request->input('direction', 'up');
        $newEp = $direction === 'up' ? $watchlist->current_episode + 1 : max(0, $watchlist->current_episode - 1);
        $totalEpisodes = $watchlist->movieSeries?->total_episodes;

        if ($totalEpisodes && $totalEpisodes > 0 && $newEp >= $totalEpisodes) {
            $newEp = $totalEpisodes;
            $watchlist->update([
                'current_episode' => $newEp,
                'status' => 'completed',
            ]);

            return back()->with('success', "🎉 Serial \"{$watchlist->movieSeries->title}\" selesai ditonton (Ep {$newEp}/{$totalEpisodes})! Siap untuk diberikan ulasan & rating.");
        }

        $watchlist->update([
            'current_episode' => $newEp,
            'status' => $newEp > 0 ? 'watching' : $watchlist->status,
        ]);

        return back()->with('success', "Progres episode diperbarui: Ep {$newEp}" . ($totalEpisodes ? "/{$totalEpisodes}" : ''));
    }

    public function updateStatus(Request $request, Watchlist $watchlist)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:plan_to_watch,watching,on_hold,dropped,completed'],
        ]);

        $watchlist->update($validated);

        return back()->with('success', 'Status berhasil diubah.');
    }

    public function destroy(Watchlist $watchlist)
    {
        $title = $watchlist->movieSeries->title;
        $watchlist->delete();

        return redirect()->route('admin.watchlist.index')->with('success', 'Item "'.$title.'" dihapus dari watchlist.');
    }
}
