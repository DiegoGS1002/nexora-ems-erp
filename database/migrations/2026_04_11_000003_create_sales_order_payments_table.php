<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();

            $table->string('payment_condition'); // À vista, 30/60, etc
            $table->string('payment_method'); // Dinheiro, Cartão, PIX, Boleto
            $table->integer('installments')->default(1);
            $table->decimal('total_amount', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_payments');
    }
};

