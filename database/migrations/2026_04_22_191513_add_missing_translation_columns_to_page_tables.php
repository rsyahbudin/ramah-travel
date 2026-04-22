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
        Schema::table('page_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('page_translations', 'content')) {
                $table->text('content')->nullable()->after('title');
            }
        });

        Schema::table('page_section_translations', function (Blueprint $table) {
            if (Schema::hasColumn('page_section_translations', 'heading') && ! Schema::hasColumn('page_section_translations', 'title')) {
                $table->renameColumn('heading', 'title');
            }
            if (Schema::hasColumn('page_section_translations', 'body') && ! Schema::hasColumn('page_section_translations', 'content')) {
                $table->renameColumn('body', 'content');
            }
        });

        Schema::table('page_section_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('page_section_translations', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (! Schema::hasColumn('page_section_translations', 'cta_text')) {
                $table->string('cta_text')->nullable()->after('content');
            }
        });

        Schema::table('page_section_feature_translations', function (Blueprint $table) {
            if (Schema::hasColumn('page_section_feature_translations', 'heading') && ! Schema::hasColumn('page_section_feature_translations', 'title')) {
                $table->renameColumn('heading', 'title');
            } elseif (! Schema::hasColumn('page_section_feature_translations', 'title')) {
                $table->string('title')->nullable()->after('language_id');
            }
        });

        Schema::table('page_section_feature_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('page_section_feature_translations', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implementing accurate rollback here since we're just trying to fix state
    }
};
