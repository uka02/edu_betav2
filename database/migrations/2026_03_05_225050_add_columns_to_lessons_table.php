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
            // Add all required columns if they don't exist
            if (!Schema::hasColumn('lessons', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('lessons', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('lessons', 'slug')) {
                $table->string('slug')->unique();
            }
            if (!Schema::hasColumn('lessons', 'type')) {
                $table->enum('type', ['video', 'text', 'document'])->default('text');
            }
            if (!Schema::hasColumn('lessons', 'content')) {
                $table->longText('content')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'document_path')) {
                $table->string('document_path')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'thumbnail')) {
                $table->string('thumbnail')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('lessons', 'difficulty')) {
                $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->nullable();
            }
            if (!Schema::hasColumn('lessons', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            if (!Schema::hasColumn('lessons', 'is_free')) {
                $table->boolean('is_free')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $columns = ['user_id', 'title', 'slug', 'type', 'content', 'video_url', 
                       'document_path', 'thumbnail', 'duration', 'difficulty', 
                       'is_published', 'is_free'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('lessons', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
