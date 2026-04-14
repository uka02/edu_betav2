<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('exam_index')->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('time_taken')->default(0);
            $table->json('answers')->nullable();
            $table->json('results')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();

            $table->index(['user_id', 'lesson_id']);
            $table->index(['lesson_id', 'passed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_exam_attempts');
    }
};
