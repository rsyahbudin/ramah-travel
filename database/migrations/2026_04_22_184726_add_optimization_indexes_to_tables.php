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
        Schema::table('page_sections', function (Blueprint $table) {
            $table->index('key');
            $table->index('sort_order');
        });

        Schema::table('page_section_features', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropIndex(['key']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('page_section_features', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });
    }
};
