<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->ensureJson('destinations', ['title', 'location', 'duration', 'theme', 'description']);
        $this->ensureJson('pages', ['title', 'content']);

        Schema::table('destinations', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('location')->change();
            $table->json('duration')->nullable()->change();
            $table->json('theme')->nullable()->change();
            $table->json('description')->change();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('content')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('location')->change();
            $table->text('duration')->nullable()->change();
            $table->text('theme')->nullable()->change();
            $table->text('description')->change();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('content')->change();
        });
    }

    protected function ensureJson(string $table, array $columns): void
    {
        foreach ($columns as $column) {
            DB::table($table)->whereNotNull($column)->get(['id', $column])->each(function ($record) use ($table, $column) {
                $value = $record->{$column};
                if (! $this->isJson($value)) {
                    DB::table($table)->where('id', $record->id)->update([
                        $column => json_encode(['en' => $value]),
                    ]);
                }
            });
        }
    }

    protected function isJson($string): bool
    {
        if (! is_string($string) || $string === '') {
            return false;
        }
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
};
