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
        Schema::create('problems', function (Blueprint $table) {
            $table->string('id')->primary(); // slug (e.g. 'two-sum')
            $table->integer('number')->unique();
            $table->string('title');
            $table->string('difficulty'); // Easy, Medium, Hard
            $table->decimal('acceptance_rate', 5, 2)->default(50.0);
            $table->json('tags');
            $table->text('summary');
            $table->json('description');
            $table->json('examples');
            $table->json('constraints');
            $table->text('follow_up')->nullable();
            $table->json('editorial');
            $table->json('starter_code');
            $table->json('testing');
            $table->json('hints');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
