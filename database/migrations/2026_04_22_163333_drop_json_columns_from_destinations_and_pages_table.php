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
            $table->dropColumn([
                'title',
                'description',
                'location',
                'duration',
                'theme',
                'highlights',
                'itinerary',
                'includes',
                'excludes',
                'faq',
                'trip_info',
            ]);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('location')->nullable();
            $table->json('duration')->nullable();
            $table->json('theme')->nullable();
            $table->json('highlights')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('includes')->nullable();
            $table->json('excludes')->nullable();
            $table->json('faq')->nullable();
            $table->json('trip_info')->nullable();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->json('title')->nullable();
            $table->json('content')->nullable();
        });
    }
};
