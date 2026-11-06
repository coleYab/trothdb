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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('problem_id');
            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->string('language'); // cpp, python, javascript
            $table->longText('code');
            $table->string('status'); // accepted, wrong_answer, time_limit, runtime_error, compile_error
            $table->integer('runtime_ms')->nullable();
            $table->decimal('memory_mb', 6, 2)->nullable();
            $table->json('test_results')->nullable();
            $table->text('stdout')->nullable();
            $table->text('stderr')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'problem_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
