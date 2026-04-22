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
        Schema::create('destination_itinerary_item_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('destination_itinerary_item_id');
            $table->foreign('destination_itinerary_item_id', 'diit_item_id_foreign')
                ->references('id')
                ->on('destination_itinerary_items')
                ->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['destination_itinerary_item_id', 'language_id'], 'itinerary_item_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_itinerary_item_translations');
    }
};
