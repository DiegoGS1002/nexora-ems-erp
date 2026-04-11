<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Fornecedor (UUID PK)
            $table->string('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->restrictOnDelete();

            // Comprador
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();

            // Status e Origem
            $table->string('status')->default('rascunho');
            $table->string('origin')->default('manual');

            // Datas
            $table->datetime('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('received_at')->nullable();

            // Pagamento
            $table->string('payment_condition')->nullable();
            $table->string('payment_method')->nullable();

            // Logística
            $table->string('freight_type')->nullable();
            $table->foreignId('carrier_id')->nullable()->constrained()->nullOnDelete();
            $table->text('delivery_address')->nullable();

            // Totais
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('shipping_amount', 14, 2)->default(0);
            $table->decimal('other_expenses', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            // Observações
            $table->text('notes')->nullable();
            $table->text('notes_supplier')->nullable();

            // Aprovação
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

