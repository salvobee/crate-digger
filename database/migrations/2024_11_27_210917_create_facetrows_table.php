<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	$tableNames = config('facet-filter.table_names');
        Schema::create($tableNames['facetrows'], function (Blueprint $table) {
            $table->id();

            $table->string('facet_slug');
            $table->foreignId('subject_id');
            $table->string('value')->nullable();

            $table->timestamps();

            $table->index(['facet_slug', 'value', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	$tableNames = config('facet-filter.table_names');
        Schema::dropIfExists($tableNames['facetrows']);
    }
};
