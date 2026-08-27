<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('movie_series_id')->constrained('movies_series')->onDelete('cascade');
            $table->decimal('rating_overall', 3, 1)->index(); // 0.5 to 5.0
            $table->decimal('rating_story', 3, 1)->nullable();
            $table->decimal('rating_visual', 3, 1)->nullable();
            $table->decimal('rating_acting', 3, 1)->nullable();
            $table->decimal('rating_audio', 3, 1)->nullable();
            $table->string('headline')->nullable();
            $table->longText('review_content')->nullable();
            $table->text('favorite_quote')->nullable();
            $table->boolean('is_spoiler')->default(false);
            $table->boolean('is_favorite')->default(false)->index();
            $table->string('watch_platform')->nullable();
            $table->date('watched_date')->nullable()->index();
            $table->integer('rewatch_count')->default(0);
            $table->text('private_notes')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
