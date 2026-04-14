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
        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'content_blocks')) {
                $contentBlocksColumn = $table->json('content_blocks')->nullable();

                if (Schema::hasColumn('lessons', 'content')) {
                    $contentBlocksColumn->after('content');
                }
            }

            if (! Schema::hasColumn('lessons', 'duration_minutes')) {
                $durationMinutesColumn = $table->integer('duration_minutes')->nullable();

                if (Schema::hasColumn('lessons', 'duration')) {
                    $durationMinutesColumn->after('duration');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('lessons', 'content_blocks')) {
                $columnsToDrop[] = 'content_blocks';
            }

            if (Schema::hasColumn('lessons', 'duration_minutes')) {
                $columnsToDrop[] = 'duration_minutes';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
