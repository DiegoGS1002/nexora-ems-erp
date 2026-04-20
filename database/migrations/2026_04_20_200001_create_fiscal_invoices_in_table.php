<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_invoices_in', function (Blueprint $table) {
            $table->id();

            // Fornecedor
            $table->string('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_cnpj', 20)->nullable();
            $table->string('supplier_ie')->nullable();

            // Pedido de compra vinculado
            $table->unsignedBigInteger('purchase_order_id')->nullable();

            // Cabeçalho fiscal
            $table->string('invoice_number', 9);
            $table->string('series', 3)->default('1');
            $table->string('access_key', 44)->nullable()->unique();
            $table->enum('doc_type', ['nfe', 'nfce', 'cte', 'nfse'])->default('nfe');
            $table->date('issue_date')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('cfop', 10)->nullable();
            $table->text('operation_nature')->nullable();

            // Status
            $table->enum('status', ['digitada', 'importada', 'validada', 'aguardando_conferencia', 'escriturada', 'cancelada'])->default('digitada');

            // Totais
            $table->decimal('products_total', 14, 2)->default(0);
            $table->decimal('shipping_total', 14, 2)->default(0);
            $table->decimal('insurance_total', 14, 2)->default(0);
            $table->decimal('other_expenses', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('total_value', 14, 2)->default(0);

            // Armazenamento XML
            $table->string('xml_path')->nullable();
            $table->text('raw_xml')->nullable();

            // Dados de integração
            $table->unsignedBigInteger('stock_movement_id')->nullable();
            $table->unsignedBigInteger('account_payable_id')->nullable();
            $table->timestamp('escriturada_at')->nullable();

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['status', 'issue_date']);
            $table->index('supplier_id');
        });

        Schema::create('fiscal_invoice_items_in', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_invoice_in_id');
            $table->string('product_id')->nullable();
            $table->string('product_code')->nullable();     // Código do fornecedor
            $table->string('product_name');
            $table->string('ncm', 10)->nullable();
            $table->string('cfop', 10)->nullable();
            $table->string('unit', 6)->default('UN');
            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('unit_price', 14, 4)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            // Impostos
            $table->decimal('icms_base', 14, 2)->default(0);
            $table->decimal('icms_rate', 8, 4)->default(0);
            $table->decimal('icms_value', 14, 2)->default(0);
            $table->decimal('ipi_value', 14, 2)->default(0);
            $table->decimal('pis_value', 14, 2)->default(0);
            $table->decimal('cofins_value', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('fiscal_invoice_in_id')->references('id')->on('fiscal_invoices_in')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_invoice_items_in');
        Schema::dropIfExists('fiscal_invoices_in');
    }
};

