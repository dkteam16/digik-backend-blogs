<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hiring_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->text('qualification')->nullable();
            $table->string('experience')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->enum('work_type', ['onsite', 'remote', 'hybrid'])->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship'])->default('full-time');
            $table->string('apply_url')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->date('application_deadline')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('author_id');
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hiring_posts');
    }
};
