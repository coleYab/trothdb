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
        Schema::create('tracks', function (Blueprint $table) {
            $table->string('id')->primary(); // slug (e.g. 'cpp-foundations')
            $table->string('title');
            $table->string('level'); // Beginner, Intermediate, Advanced
            $table->integer('hours');
            $table->text('summary');
            $table->text('description');
            $table->json('prerequisites');
            $table->string('continue_lesson')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->string('id')->primary(); // slug (e.g. 'value-categories')
            $table->string('track_id');
            $table->foreign('track_id')->references('id')->on('tracks')->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->text('summary');
            $table->integer('minutes');
            $table->integer('order')->default(0);
            $table->string('module_title');
            $table->text('module_summary')->nullable();
            $table->json('blocks');
            $table->json('quiz');
            $table->text('assembly')->nullable();
            $table->json('discussion')->nullable();
            $table->timestamps();

            $table->index(['track_id', 'slug']);
        });

        Schema::create('user_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
            $table->boolean('completed')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lesson_progress');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('tracks');
    }
};
