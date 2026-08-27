<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    protected string $baseUrl = 'https://api.themoviedb.org/3';

    protected ?string $apiKey = null;

    public function __construct()
    {
        $owner = User::getOwner();
        $this->apiKey = $owner?->tmdb_api_key ?? config('services.tmdb.api_key', env('TMDB_API_KEY'));
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function search(string $query, string $type = 'all', int $page = 1): array
    {
        if (empty($this->apiKey) || empty(trim($query))) {
            return [];
        }

        try {
            $endpoint = match ($type) {
                'movie' => '/search/movie',
                'tv', 'series' => '/search/tv',
                default => '/search/multi',
            };

            $response = Http::timeout(8)->get("{$this->baseUrl}{$endpoint}", [
                'api_key' => $this->apiKey,
                'query' => $query,
                'page' => $page,
                'include_adult' => false,
                'language' => 'id-ID,en-US',
            ]);

            if ($response->successful()) {
                $results = $response->json('results', []);

                return array_values(array_filter(array_map(function ($item) {
                    $mediaType = $item['media_type'] ?? ($item['title'] ?? null ? 'movie' : 'tv');
                    if (! in_array($mediaType, ['movie', 'tv'])) {
                        return null;
                    }

                    $title = $item['title'] ?? $item['name'] ?? 'Unknown';
                    $originalTitle = $item['original_title'] ?? $item['original_name'] ?? null;
                    $date = $item['release_date'] ?? $item['first_air_date'] ?? null;
                    $year = $date ? (int) substr($date, 0, 4) : null;

                    return [
                        'tmdb_id' => $item['id'],
                        'type' => $mediaType === 'tv' ? 'series' : 'movie',
                        'title' => $title,
                        'original_title' => $originalTitle,
                        'release_year' => $year,
                        'release_date' => $date,
                        'synopsis' => $item['overview'] ?? '',
                        'poster_url' => $item['poster_path'] ? "https://image.tmdb.org/t/p/w500{$item['poster_path']}" : null,
                        'backdrop_url' => $item['backdrop_path'] ? "https://image.tmdb.org/t/p/w1280{$item['backdrop_path']}" : null,
                        'vote_average' => $item['vote_average'] ?? null,
                    ];
                }, $results)));
            }
        } catch (\Exception $e) {
            Log::error('TMDB search error: '.$e->getMessage());
        }

        return [];
    }

    public function getDetails(int $tmdbId, string $type = 'movie'): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $endpoint = ($type === 'tv' || $type === 'series') ? "/tv/{$tmdbId}" : "/movie/{$tmdbId}";

            $response = Http::timeout(10)->get("{$this->baseUrl}{$endpoint}", [
                'api_key' => $this->apiKey,
                'append_to_response' => 'credits,keywords',
                'language' => 'id-ID,en-US',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $isTv = ($type === 'tv' || $type === 'series');

                $title = $data['title'] ?? $data['name'] ?? '';
                $originalTitle = $data['original_title'] ?? $data['original_name'] ?? null;
                $date = $data['release_date'] ?? $data['first_air_date'] ?? null;
                $year = $date ? (int) substr($date, 0, 4) : null;

                // Extract director / creator
                $director = null;
                if ($isTv) {
                    $creators = array_column($data['created_by'] ?? [], 'name');
                    $director = ! empty($creators) ? implode(', ', $creators) : null;
                } else {
                    $crew = $data['credits']['crew'] ?? [];
                    foreach ($crew as $member) {
                        if (($member['job'] ?? '') === 'Director') {
                            $director = $member['name'];
                            break;
                        }
                    }
                }

                // Extract top cast members
                $cast = [];
                $castList = $data['credits']['cast'] ?? [];
                foreach (array_slice($castList, 0, 8) as $member) {
                    $cast[] = [
                        'name' => $member['name'],
                        'character' => $member['character'] ?? '',
                        'profile_url' => $member['profile_path'] ? "https://image.tmdb.org/t/p/w185{$member['profile_path']}" : null,
                    ];
                }

                // Extract genres
                $genres = array_column($data['genres'] ?? [], 'name');

                return [
                    'tmdb_id' => $data['id'],
                    'type' => $isTv ? 'series' : 'movie',
                    'title' => $title,
                    'original_title' => $originalTitle,
                    'release_year' => $year,
                    'release_date' => $date,
                    'synopsis' => $data['overview'] ?? '',
                    'poster_url' => $data['poster_path'] ? "https://image.tmdb.org/t/p/w500{$data['poster_path']}" : null,
                    'backdrop_url' => $data['backdrop_path'] ? "https://image.tmdb.org/t/p/w1280{$data['backdrop_path']}" : null,
                    'director' => $director,
                    'cast_members' => $cast,
                    'genres' => $genres,
                    'runtime_minutes' => $data['runtime'] ?? ($data['episode_run_time'][0] ?? ($data['last_episode_to_air']['runtime'] ?? null)),
                    'total_seasons' => $data['number_of_seasons'] ?? null,
                    'total_episodes' => $data['number_of_episodes'] ?? null,
                    'original_language' => strtoupper($data['original_language'] ?? 'en'),
                ];
            }
        } catch (\Exception $e) {
            Log::error('TMDB details error: '.$e->getMessage());
        }

        return null;
    }
}
