<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->string('io_mode', 32)->default('harness')->after('starter_code');
            $table->integer('total_points')->default(100)->after('io_mode');
            $table->integer('time_limit_ms')->default(2000)->after('total_points');
            $table->integer('memory_limit_mb')->default(256)->after('time_limit_ms');
            $table->integer('version')->default(1)->after('memory_limit_mb');
            $table->boolean('is_active')->default(true)->after('version');
        });

        Schema::create('problem_test_cases', function (Blueprint $table) {
            $table->id();
            $table->string('problem_id', 64);
            $table->string('case_id', 64);
            $table->string('name', 255);
            $table->string('group', 32)->default('basic');
            $table->string('file_path', 255);
            $table->mediumText('test_code_cpp');
            $table->integer('points')->default(10);
            $table->integer('time_limit_ms')->default(2000);
            $table->boolean('is_hidden')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->unique(['problem_id', 'case_id']);
            $table->index(['problem_id', 'is_hidden', 'order_index']);
        });

        Schema::create('problem_assets', function (Blueprint $table) {
            $table->id();
            $table->string('problem_id', 64);
            $table->string('file_path', 255);
            $table->string('file_type', 32);
            $table->mediumText('content');
            $table->char('sha256_hash', 64);
            $table->boolean('is_secret')->default(false);
            $table->timestamps();

            $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            $table->unique(['problem_id', 'file_path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_assets');
        Schema::dropIfExists('problem_test_cases');

        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn([
                'io_mode',
                'total_points',
                'time_limit_ms',
                'memory_limit_mb',
                'version',
                'is_active',
            ]);
        });
    }
};
