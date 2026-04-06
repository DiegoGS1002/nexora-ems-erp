<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20)->default('success'); // success | warning | error
            $table->string('action', 80);                    // LOGIN, CRIACAO, ALTERACAO, EXCLUSAO ...
            $table->string('module', 80)->default('Sistema');
            $table->text('description');
            $table->string('ip', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['level', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};

