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
        Schema::create('device_authorizations', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 64)->unique();
            $table->string('user_code', 16)->unique();
            $table->string('client_name')->default('LocalJudge');
            $table->integer('callback_port')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('token')->nullable();
            $table->string('status')->default('pending'); // pending, approved, denied
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['device_code', 'status']);
            $table->index(['user_code', 'status']);
        });

        Schema::create('cli_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('LocalJudge CLI');
            $table->string('token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cli_tokens');
        Schema::dropIfExists('device_authorizations');
    }
};
