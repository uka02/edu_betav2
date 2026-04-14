<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'issued_by_user_id')) {
                $table->foreignId('issued_by_user_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('certificates', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('issued_at');
            }

            if (! Schema::hasColumn('certificates', 'validation_notes')) {
                $table->text('validation_notes')->nullable()->after('validated_at');
            }
        });

        if (Schema::hasColumn('certificates', 'issued_by_user_id')) {
            $lessonIssuerMap = DB::table('lessons')
                ->pluck('user_id', 'id');

            DB::table('certificates')
                ->select(['id', 'lesson_id'])
                ->whereNull('issued_by_user_id')
                ->orderBy('id')
                ->get()
                ->each(function (object $certificate) use ($lessonIssuerMap) {
                    $issuerId = $lessonIssuerMap[$certificate->lesson_id] ?? null;

                    if ($issuerId !== null) {
                        DB::table('certificates')
                            ->where('id', $certificate->id)
                            ->update([
                                'issued_by_user_id' => $issuerId,
                            ]);
                    }
                });
        }

        if (Schema::hasColumn('certificates', 'validated_at')) {
            DB::table('certificates')
                ->whereNull('validated_at')
                ->update([
                    'validated_at' => DB::raw('issued_at'),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'issued_by_user_id')) {
                $table->dropConstrainedForeignId('issued_by_user_id');
            }

            if (Schema::hasColumn('certificates', 'validated_at')) {
                $table->dropColumn('validated_at');
            }

            if (Schema::hasColumn('certificates', 'validation_notes')) {
                $table->dropColumn('validation_notes');
            }
        });
    }
};
