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
        Schema::create('analyses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('type');
            $table->string('resource_id');
            $table->string('status')->default('created');

            $table->integer('jobs')->default(0);
            $table->integer('processed')->default(0);
            $table->integer('failed')->default(0);

            $table->string('batch_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
