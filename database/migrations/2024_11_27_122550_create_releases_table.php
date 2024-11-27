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
        Schema::create('releases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('discogs_id')->unique();
            $table->string('artist');
            $table->string('title');
            $table->string('label');
            $table->string('year');
            $table->string('catalog_number');
            // Optional fields
            $table->integer('want')->nullable();
            $table->integer('have')->nullable();

            // Additional fields to be added later
            $table->decimal('rating_average', 2, 1)->nullable();
            $table->integer('rating_count')->nullable();

            $table->json('videos')->nullable();

            $table->string('master_id')->nullable();
            $table->integer('num_for_sale')->nullable();
            $table->decimal('lowest_price', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
