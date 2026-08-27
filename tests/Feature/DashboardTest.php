<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\MovieSeries;
use App\Models\Review;
use App\Models\User;
use App\Models\Watchlist;
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

    public function test_series_watch_time_calculates_episodes_multiplied_by_runtime_minutes(): void
    {
        $user = User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $series = MovieSeries::create([
            'title' => 'Arcane',
            'type' => 'series',
            'slug' => 'arcane',
            'runtime_minutes' => 45,
            'total_episodes' => 10,
        ]);

        Review::create([
            'user_id' => $user->id,
            'movie_series_id' => $series->id,
            'rating_overall' => 9.5,
            'headline' => 'Fantastic series',
            'is_published' => true,
        ]);

        // 45 mins * 10 eps = 450 mins = 8 hours (rounded from 7.5 hours)
        $this->assertEquals(450, $series->total_runtime_minutes);

        $response = $this->get('/');
        $response->assertStatus(200);
        // Assert 8 Jam is displayed in the stats
        $response->assertSee('8 <span class="text-sm text-zinc-400">Jam</span>', false);
    }

    public function test_watchlist_progress_and_completion_triggers_needs_review_badge(): void
    {
        $user = User::create([
            'name' => 'Owner Test',
            'username' => 'ownertest',
            'email' => 'owner@cinelog.test',
            'password' => bcrypt('secretpassword123'),
            'is_setup_completed' => true,
        ]);

        $series = MovieSeries::create([
            'title' => 'Shogun',
            'type' => 'series',
            'slug' => 'shogun',
            'runtime_minutes' => 60,
            'total_episodes' => 10,
        ]);

        $watchlist = Watchlist::create([
            'user_id' => $user->id,
            'movie_series_id' => $series->id,
            'status' => 'watching',
            'current_season' => 1,
            'current_episode' => 5,
        ]);

        // 5 / 10 = 50%
        $this->assertEquals(50, $watchlist->progress_percentage);
        $this->assertFalse($watchlist->is_finished);
        $this->assertFalse($watchlist->needs_review);

        // Advance to 10th episode via progress route
        $response = $this->actingAs($user)->post(route('admin.watchlist.progress', $watchlist), [
            'direction' => 'up',
        ]);

        // After updating to completed status
        $watchlist->update(['current_episode' => 10, 'status' => 'completed']);
        $this->assertEquals(100, $watchlist->progress_percentage);
        $this->assertTrue($watchlist->is_finished);
        $this->assertTrue($watchlist->needs_review);

        // Admin layout renders badge with count 1
        $adminResponse = $this->actingAs($user)->get(route('admin.dashboard'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Ada 1 Tontonan Selesai Siap Diberi Rating!');
    }
}
