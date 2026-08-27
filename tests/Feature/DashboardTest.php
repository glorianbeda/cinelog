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
            'rating_overall' => 9.5,
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

    public function test_review_can_be_stored_with_10_point_rating(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@cinelog.test',
            'password' => 'password',
            'is_setup_completed' => true,
        ]);

        $response = $this->actingAs($user)->post('/admin/reviews', [
            'title' => 'Dune Part Two',
            'type' => 'movie',
            'rating_overall' => 9.5,
            'rating_story' => 10.0,
            'rating_visual' => 10.0,
            'headline' => 'Visual spectacle',
            'review_content' => 'Spectacular film',
            'watched_date' => '2026-08-27',
        ]);

        $response->assertRedirect('/admin/reviews');
        $this->assertDatabaseHas('reviews', [
            'rating_overall' => 9.5,
            'rating_story' => 10.0,
            'headline' => 'Visual spectacle',
        ]);
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

    public function test_unauthenticated_guest_accessing_admin_gets_404_not_found(): void
    {
        User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $response = $this->get('/admin');
        $response->assertStatus(404);
    }

    public function test_standard_login_and_probe_urls_return_404_not_found(): void
    {
        User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $this->get('/login')->assertStatus(404);
        $this->get('/admin/login')->assertStatus(404);
        $this->get('/wp-admin')->assertStatus(404);
    }

    public function test_secret_login_url_is_accessible_and_allows_login(): void
    {
        $user = User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $secretPath = config('auth.admin_login_path', 'vault-gate');

        // Secret login portal loads successfully
        $response = $this->get('/' . $secretPath);
        $response->assertStatus(200);

        // Authentication works via secret portal
        $postResponse = $this->post('/' . $secretPath, [
            'email' => 'owner@cinelog.test',
            'password' => 'secretpassword123',
        ]);

        $postResponse->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_public_home_does_not_contain_any_login_links_or_buttons_for_guests(): void
    {
        User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('/vault-gate');
        $response->assertDontSee('Masuk Pengelola');
        $response->assertDontSee('Panel Admin');
    }
}
