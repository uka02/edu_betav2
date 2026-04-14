<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificate_verifications')) {
            return;
        }

        Schema::table('certificate_verifications', function (Blueprint $table) {
            if (! Schema::hasColumn('certificate_verifications', 'lesson_id')) {
                $table->foreignId('lesson_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('certificate_verifications', 'passing_score')) {
                $table->unsignedTinyInteger('passing_score')
                    ->nullable()
                    ->after('title');
            }
        });

        Schema::whenTableDoesntHaveIndex('certificate_verifications', 'certificate_verifications_lesson_id_status_index', function (Blueprint $table) {
                $table->index(['lesson_id', 'status'], 'certificate_verifications_lesson_id_status_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificate_verifications')) {
            return;
        }

        Schema::whenTableHasIndex('certificate_verifications', 'certificate_verifications_lesson_id_status_index', function (Blueprint $table) {
            $table->dropIndex('certificate_verifications_lesson_id_status_index');
        });

        Schema::table('certificate_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_verifications', 'lesson_id')) {
                $table->dropConstrainedForeignId('lesson_id');
            }

            if (Schema::hasColumn('certificate_verifications', 'passing_score')) {
                $table->dropColumn('passing_score');
            }
        });
    }
};
