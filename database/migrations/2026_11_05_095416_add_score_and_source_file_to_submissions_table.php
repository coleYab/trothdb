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
        Schema::table('submissions', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable()->after('status');
            $table->string('source_file_name')->nullable()->after('code');
            $table->string('source_file_path')->nullable()->after('source_file_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['score', 'source_file_name', 'source_file_path']);
        });
    }
};
