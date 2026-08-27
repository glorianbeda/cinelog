<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Http\Request;

class PublicWatchlistController extends Controller
{
    public function index(Request $request)
    {
        $owner = User::getOwner();

        $status = $request->input('status', 'all');

        $query = Watchlist::with(['movieSeries.genres', 'movieSeries.reviews'])
            ->latest('updated_at');

        if ($status !== 'all' && in_array($status, ['watching', 'plan_to_watch', 'completed', 'on_hold', 'dropped'])) {
            $query->where('status', $status);
        }

        $items = $query->paginate(12)->withQueryString();

        $counts = [
            'all' => Watchlist::count(),
            'watching' => Watchlist::where('status', 'watching')->count(),
            'plan_to_watch' => Watchlist::where('status', 'plan_to_watch')->count(),
            'completed' => Watchlist::where('status', 'completed')->count(),
            'on_hold' => Watchlist::where('status', 'on_hold')->count(),
        ];

        return view('watchlist.index', compact('owner', 'items', 'counts', 'status'));
    }
}
