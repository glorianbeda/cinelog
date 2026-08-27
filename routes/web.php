<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TmdbProxyController;
use App\Http\Controllers\Admin\WatchlistController as AdminWatchlistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicWatchlistController;
use App\Http\Controllers\ReviewDetailController;
use App\Http\Controllers\SetupOwnerController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/reviews', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/reviews/{slug}', [ReviewDetailController::class, 'show'])->name('reviews.show');
Route::get('/watchlist', [PublicWatchlistController::class, 'index'])->name('watchlist.public');
Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

// One-Time First Setup Wizard
Route::middleware(['prevent.duplicate.setup'])->group(function () {
    Route::get('/setup-owner', [SetupOwnerController::class, 'show'])->name('setup.show');
    Route::post('/setup-owner', [SetupOwnerController::class, 'store'])->name('setup.store');
});

Route::get('/setup-storage', function () {
    Artisan::call('storage:link');

    return 'Storage berhasil di-link!';
});

// Authentication
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Panel (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Reviews Management
    Route::resource('reviews', AdminReviewController::class);

    // Watchlist Management
    Route::resource('watchlist', AdminWatchlistController::class)->except(['show', 'edit']);
    Route::post('/watchlist/{watchlist}/progress', [AdminWatchlistController::class, 'updateProgress'])->name('watchlist.progress');
    Route::put('/watchlist/{watchlist}/status', [AdminWatchlistController::class, 'updateStatus'])->name('watchlist.status');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');

    // TMDB Proxy API
    Route::get('/api/tmdb/search', [TmdbProxyController::class, 'search'])->name('tmdb.search');
    Route::get('/api/tmdb/details/{type}/{id}', [TmdbProxyController::class, 'details'])->name('tmdb.details');
});
