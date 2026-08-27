<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TmdbProxyController extends Controller
{
    public function __construct(protected TmdbService $tmdbService) {}

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');

        if (empty(trim($query))) {
            return response()->json(['results' => [], 'has_key' => !empty($this->tmdbService->getApiKey())]);
        }

        $results = $this->tmdbService->search($query, $type);

        return response()->json([
            'results' => $results,
            'has_key' => !empty($this->tmdbService->getApiKey()),
        ]);
    }

    public function details(string $type, int $id): JsonResponse
    {
        $details = $this->tmdbService->getDetails($id, $type);

        if (!$details) {
            return response()->json(['error' => 'Gagal mengambil detail dari TMDB atau API Key belum diisi.'], 404);
        }

        return response()->json($details);
    }
}
