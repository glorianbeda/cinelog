<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('movie_series_id')->constrained('movies_series')->onDelete('cascade');
            $table->enum('status', ['plan_to_watch', 'watching', 'on_hold', 'dropped', 'completed'])->default('plan_to_watch')->index();
            $table->integer('current_season')->default(1);
            $table->integer('current_episode')->default(0);
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->string('watch_platform')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
