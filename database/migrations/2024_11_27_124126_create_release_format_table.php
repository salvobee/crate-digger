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
        Schema::create('release_format', function (Blueprint $table) {
            $table->foreignUuid('release_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('format_id')->constrained()->cascadeOnDelete();
            $table->primary(['release_id', 'format_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_format');
    }
};
