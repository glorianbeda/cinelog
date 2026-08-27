<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->integer('tmdb_genre_id')->nullable()->index();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('movie_genres', function (Blueprint $table) {
            $table->foreignId('movie_series_id')->constrained('movies_series')->onDelete('cascade');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->primary(['movie_series_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_genres');
        Schema::dropIfExists('genres');
    }
};
