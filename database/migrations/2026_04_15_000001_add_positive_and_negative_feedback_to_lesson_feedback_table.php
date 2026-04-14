<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_feedback', function (Blueprint $table) {
            $table->text('positive_feedback')->nullable()->after('feedback');
            $table->text('negative_feedback')->nullable()->after('positive_feedback');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_feedback', function (Blueprint $table) {
            $table->dropColumn(['positive_feedback', 'negative_feedback']);
        });
    }
};
