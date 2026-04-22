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
        Schema::create('destination_trip_info_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_trip_info_id');
            $table->foreign('destination_trip_info_id', 'dtit_trip_info_id_foreign')
                ->references('id')
                ->on('destination_trip_infos')
                ->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->timestamps();

            $table->unique(['destination_trip_info_id', 'language_id'], 'trip_info_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_trip_info_translations');
    }
};
