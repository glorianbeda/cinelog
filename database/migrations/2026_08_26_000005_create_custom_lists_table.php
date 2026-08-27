<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('custom_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_list_id')->constrained('custom_lists')->onDelete('cascade');
            $table->foreignId('movie_series_id')->constrained('movies_series')->onDelete('cascade');
            $table->integer('order_position')->default(0);
            $table->text('item_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_list_items');
        Schema::dropIfExists('custom_lists');
    }
};
