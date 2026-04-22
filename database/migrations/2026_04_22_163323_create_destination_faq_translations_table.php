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
        Schema::create('destination_faq_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_faq_id')
                ->constrained('destination_faqs')
                ->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->timestamps();

            $table->unique(['destination_faq_id', 'language_id'], 'dest_faq_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_faq_translations');
    }
};
