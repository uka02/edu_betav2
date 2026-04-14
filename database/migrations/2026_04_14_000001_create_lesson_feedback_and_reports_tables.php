<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
            $table->index(['lesson_id', 'created_at'], 'lesson_feedback_lesson_created_at_index');
        });

        Schema::create('lesson_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 50);
            $table->text('details');
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
            $table->index(['lesson_id', 'created_at'], 'lesson_reports_lesson_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_reports');
        Schema::dropIfExists('lesson_feedback');
    }
};
