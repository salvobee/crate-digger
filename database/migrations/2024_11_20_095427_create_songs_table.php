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
        Schema::create('songs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('artist_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignUuid('label_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('artist_name');
            $table->string('name');

            $table->string('version')->nullable();
            $table->string('year')->nullable();
            $table->string('label_name')->nullable();

            $table->string('discogs_master_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
