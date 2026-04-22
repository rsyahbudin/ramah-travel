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
        Schema::table('settings', function (Blueprint $table) {
            // 'text' = plain non-translatable value stored in settings.value
            // 'translatable' = translated via setting_translations table
            // 'boolean' = true/false stored in settings.value
            // 'json' = structured JSON stored in settings.value (non-translatable)
            $table->enum('type', ['text', 'translatable', 'boolean', 'json'])
                ->default('text')
                ->after('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
