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
            $table->json('itinerary')->nullable()->after('highlights');
            $table->unsignedInteger('person')->nullable()->after('itinerary');
            $table->json('includes')->nullable()->after('person');
            $table->json('excludes')->nullable()->after('includes');
            $table->json('faq')->nullable()->after('excludes');
            $table->json('trip_info')->nullable()->after('faq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['itinerary', 'person', 'includes', 'excludes', 'faq', 'trip_info']);
        });
    }
};
