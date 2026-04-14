<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->index(
                ['is_published', 'created_at'],
                'lessons_published_created_at_index'
            );
            $table->index(
                ['user_id', 'is_published', 'created_at'],
                'lessons_user_published_created_at_index'
            );
            $table->index(
                ['is_published', 'subject'],
                'lessons_published_subject_index'
            );
            $table->index(
                ['is_published', 'difficulty', 'is_free', 'created_at'],
                'lessons_published_difficulty_is_free_created_at_index'
            );
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->index(
                ['user_id', 'last_viewed_at'],
                'lesson_progress_user_last_viewed_at_index'
            );
            $table->index(
                ['lesson_id', 'user_id'],
                'lesson_progress_lesson_user_index'
            );
            $table->index(
                ['lesson_id', 'last_viewed_at'],
                'lesson_progress_lesson_last_viewed_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex('lesson_progress_lesson_last_viewed_at_index');
            $table->dropIndex('lesson_progress_lesson_user_index');
            $table->dropIndex('lesson_progress_user_last_viewed_at_index');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_published_difficulty_is_free_created_at_index');
            $table->dropIndex('lessons_published_subject_index');
            $table->dropIndex('lessons_user_published_created_at_index');
            $table->dropIndex('lessons_published_created_at_index');
        });
    }
};
