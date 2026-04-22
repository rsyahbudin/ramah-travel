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
        Schema::table('destination_translations', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('page_translations', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('destination_itinerary_item_translations', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('destination_item_translations', function (Blueprint $table) {
            $table->string('label')->nullable()->change();
        });

        Schema::table('destination_faq_translations', function (Blueprint $table) {
            $table->string('question')->nullable()->change();
            $table->text('answer')->nullable()->change();
        });

        Schema::table('destination_trip_info_translations', function (Blueprint $table) {
            $table->string('label')->nullable()->change();
            $table->text('value')->nullable()->change();
        });

        Schema::table('page_section_translations', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('page_section_feature_translations', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('setting_translations', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback for nullability in this context
    }
};
