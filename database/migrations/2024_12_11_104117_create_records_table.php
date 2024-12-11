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
        Schema::create('records', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('discogs_id');
            $table->string('discogs_url');
            $table->string('discogs_main_release_id');
            $table->string('discogs_most_recent_release_id');

            $table->json('artists');
            $table->string('title');
            $table->integer('year')->nullable();

            $table->schemalessAttributes('meta');
            $table->timestamps();
        });

        Schema::create('record_genre', function (Blueprint $table) {
            $table->uuid('record_id');
            $table->uuid('genre_id');
            $table->primary(['record_id', 'genre_id']);
        });

        Schema::create('record_style', function (Blueprint $table) {
            $table->uuid('record_id');
            $table->uuid('style_id');
            $table->primary(['record_id', 'style_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_style');
        Schema::dropIfExists('record_genre');
        Schema::dropIfExists('records');
    }
};
