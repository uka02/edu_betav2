<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_exam_attempt_id')->nullable()->constrained('lesson_exam_attempts')->nullOnDelete();
            $table->unsignedInteger('exam_index')->default(0);
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at');
            $table->json('snapshot');
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
