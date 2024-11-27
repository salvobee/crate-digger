<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('release_genre', function (Blueprint $table) {
            $table->foreignUuid('release_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['release_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_genre');
    }
};
