<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('action'); // created, updated, status_changed, approved, cancelled, etc
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // Dados completos da alteração
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['sales_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_logs');
    }
};

