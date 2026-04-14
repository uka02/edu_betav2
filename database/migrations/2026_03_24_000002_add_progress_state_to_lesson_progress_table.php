<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_progress', 'progress_state')) {
                $table->json('progress_state')->nullable()->after('progress_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_progress', 'progress_state')) {
                $table->dropColumn('progress_state');
            }
        });
    }
};
