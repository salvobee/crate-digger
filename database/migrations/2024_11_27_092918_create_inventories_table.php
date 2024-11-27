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
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('seller_username');
            $table->string('seller_id');
            $table->string('html_url');
            $table->string('avatar_url')
                ->nullable();

            $table->decimal('rating', 5)
                ->unsigned()
                ->nullable();
            $table->decimal('stars', 3, 1)
                ->unsigned()
                ->nullable();
            $table->decimal('total_feedbacks')
                ->nullable();

            $table->decimal('min_order_total', 8, 2)
                ->nullable();

            $table->unsignedInteger('total_listings_count')
                ->nullable();
            $table->dateTime('total_listings_count_updated_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
