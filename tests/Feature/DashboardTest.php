<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_loads_successfully_on_sqlite(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@cinelog.test',
            'password' => 'password',
            'is_setup_completed' => true,
        ]);

        // Create genres
        $genreSciFi = Genre::create(['name' => 'Sci-Fi', 'slug' => 'sci-fi']);
        $genreDrama = Genre::create(['name' => 'Drama', 'slug' => 'drama']);
        $genreEmpty = Genre::create(['name' => 'Horror', 'slug' => 'horror']);

        // Create movie
        $movie = MovieSeries::create([
            'title' => 'Interstellar',
            'slug' => 'interstellar-2014',
            'type' => 'movie',
            'release_year' => 2014,
        ]);

        $movie->genres()->attach([$genreSciFi->id, $genreDrama->id]);

        // Create review
        Review::create([
            'user_id' => $user->id,
            'movie_series_id' => $movie->id,
            'rating_overall' => 5.0,
            'headline' => 'Masterpiece',
            'review_content' => 'Exceptional movie',
            'watched_date' => now(),
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Admin Test');
        $response->assertSee('Sci-Fi');
        $response->assertSee('Drama');
    }

    public function test_setup_owner_flow_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/setup-owner', [
            'name' => 'Owner Name',
            'username' => 'owner',
            'email' => 'owner@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'bio' => 'Owner bio description',
        ]);

        $response->assertRedirect('/admin');

        $this->assertAuthenticated();

        // Follow redirect to ensure admin dashboard loads without SQLite error
        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Owner Name');
    }
}
