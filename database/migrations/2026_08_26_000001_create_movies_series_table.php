<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies_series', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->index();
            $table->enum('type', ['movie', 'series', 'anime'])->default('movie')->index();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->string('slug')->unique();
            $table->integer('release_year')->nullable()->index();
            $table->date('release_date')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('backdrop_url')->nullable();
            $table->string('director')->nullable();
            $table->json('cast_members')->nullable();
            $table->integer('runtime_minutes')->nullable();
            $table->integer('total_seasons')->nullable();
            $table->integer('total_episodes')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->boolean('is_custom_entry')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies_series');
    }
};
