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
        Schema::create('listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inventory_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('release_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('discogs_id')
                ->unique();

            $table->decimal('price_value', 8, 2);
            $table->string('price_currency', 3);

            $table->string('media_condition');
            $table->string('sleeve_condition');
            $table->string('comments')->nullable();
            $table->string('ships_from');
            $table->boolean('allow_offers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
