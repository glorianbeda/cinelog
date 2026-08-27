<?php

namespace App\Providers;

use App\Models\Watchlist;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.admin', 'layouts.app', 'admin.*', 'watchlist.*'], function ($view) {
            try {
                if (Schema::hasTable('watchlists') && Schema::hasTable('reviews')) {
                    $pendingReviewsCount = Watchlist::where('status', 'completed')
                        ->whereDoesntHave('movieSeries.reviews')
                        ->count();
                    $view->with('pendingReviewsCount', $pendingReviewsCount);
                }
            } catch (\Throwable $e) {
                $view->with('pendingReviewsCount', 0);
            }
        });
    }
}
