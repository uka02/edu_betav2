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
            // Add segments column for new segment-based architecture
            if (! Schema::hasColumn('lessons', 'segments')) {
                $segmentsColumn = $table->json('segments')->nullable();

                if (Schema::hasColumn('lessons', 'content')) {
                    $segmentsColumn->after('content');
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
            if (Schema::hasColumn('lessons', 'segments')) {
                $table->dropColumn('segments');
            }
        });
    }
};
