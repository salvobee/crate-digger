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
        Schema::table('releases', function (Blueprint $table) {
            $table->dropUnique('releases_discogs_id_unique');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropUnique('listings_discogs_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->string('discogs_id')->unique()->change();
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->string('discogs_id')->unique()->change();
        });
    }
};
