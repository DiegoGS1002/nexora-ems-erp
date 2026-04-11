<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->enum('type', ['billing', 'delivery', 'collection']); // faturamento, entrega, cobrança

            $table->string('zip_code', 10)->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('country')->default('Brasil');
            $table->string('ibge_code', 10)->nullable();

            $table->timestamps();

            $table->index(['sales_order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_addresses');
    }
};

