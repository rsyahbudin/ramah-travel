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
        Schema::table('destinations', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('location')->change();
            $table->text('duration')->nullable()->change();
            $table->text('theme')->nullable()->change();
            $table->text('description')->change();
            // highlights, itinerary, includes, excludes, faq, trip_info are already JSON/array
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('content')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('location')->change();
            $table->string('duration')->nullable()->change();
            $table->string('theme')->nullable()->change();
            $table->text('description')->change();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('content')->change();
        });
    }
};
