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
        Schema::create('positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('chart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('order');
            $table->string('name');

            $table->foreignUuid('song_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('last_week_position')->nullable();
            $table->integer('song_position_peak')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
