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
        Schema::create('user_list_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_list_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type');
            $table->string('discogs_id');
            $table->string('discogs_url');
            $table->string('display_title');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_list_items');
    }
};
