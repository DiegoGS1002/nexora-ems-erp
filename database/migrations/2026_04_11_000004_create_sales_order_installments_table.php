<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_payment_id')->constrained('sales_order_payments')->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();

            $table->integer('installment_number');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->string('status')->default('pending'); // pending, paid, overdue, cancelled
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sales_order_id', 'installment_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_installments');
    }
};

