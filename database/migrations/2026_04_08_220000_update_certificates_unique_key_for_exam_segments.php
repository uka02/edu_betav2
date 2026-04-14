<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::whenTableDoesntHaveIndex('certificates', 'certificates_user_id_index', function (Blueprint $table) {
            $table->index('user_id', 'certificates_user_id_index');
        });

        Schema::whenTableDoesntHaveIndex('certificates', 'certificates_lesson_id_index', function (Blueprint $table) {
            $table->index('lesson_id', 'certificates_lesson_id_index');
        });

        Schema::whenTableDoesntHaveIndex('certificates', 'certificates_user_lesson_exam_unique', function (Blueprint $table) {
            $table->unique(['user_id', 'lesson_id', 'exam_index'], 'certificates_user_lesson_exam_unique');
        }, 'unique');

        Schema::whenTableHasIndex('certificates', 'certificates_user_id_lesson_id_unique', function (Blueprint $table) {
            $table->dropUnique('certificates_user_id_lesson_id_unique');
        }, 'unique');
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::whenTableHasIndex('certificates', 'certificates_user_lesson_exam_unique', function (Blueprint $table) {
            $table->dropUnique('certificates_user_lesson_exam_unique');
        }, 'unique');

        Schema::whenTableDoesntHaveIndex('certificates', 'certificates_user_id_lesson_id_unique', function (Blueprint $table) {
            $table->unique(['user_id', 'lesson_id'], 'certificates_user_id_lesson_id_unique');
        }, 'unique');
    }
};
