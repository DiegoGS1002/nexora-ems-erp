<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            // 4. Itens do Pedido - Campos adicionais
            $table->string('sku')->nullable()->after('product_id');
            $table->string('ean')->nullable()->after('sku');
            $table->text('description')->nullable()->after('ean');
            $table->string('unit')->nullable()->after('description'); // UN, KG, CX, etc

            $table->decimal('discount_percent', 5, 2)->default(0)->after('discount');
            $table->decimal('addition', 15, 2)->default(0)->after('discount_percent');
            $table->decimal('addition_percent', 5, 2)->default(0)->after('addition');

            // Controle avançado de estoque
            $table->string('batch')->nullable()->after('product_id');
            $table->date('expiry_date')->nullable()->after('batch');
            $table->string('serial_number')->nullable()->after('expiry_date');
            $table->string('stock_location')->nullable()->after('serial_number');

            // 6. Impostos por item
            $table->string('cfop', 10)->nullable()->after('tax_amount');
            $table->string('ncm', 10)->nullable()->after('cfop');
            $table->string('cst', 5)->nullable()->after('ncm');
            $table->string('csosn', 5)->nullable()->after('cst');
            $table->string('origin', 1)->nullable()->after('csosn'); // Origem da mercadoria

            $table->decimal('icms_base', 15, 2)->default(0)->after('origin');
            $table->decimal('icms_percent', 5, 2)->default(0)->after('icms_base');
            $table->decimal('icms_amount', 15, 2)->default(0)->after('icms_percent');

            $table->decimal('icms_st_base', 15, 2)->default(0)->after('icms_amount');
            $table->decimal('icms_st_percent', 5, 2)->default(0)->after('icms_st_base');
            $table->decimal('icms_st_amount', 15, 2)->default(0)->after('icms_st_percent');

            $table->decimal('ipi_base', 15, 2)->default(0)->after('icms_st_amount');
            $table->decimal('ipi_percent', 5, 2)->default(0)->after('ipi_base');
            $table->decimal('ipi_amount', 15, 2)->default(0)->after('ipi_percent');

            $table->decimal('pis_base', 15, 2)->default(0)->after('ipi_amount');
            $table->decimal('pis_percent', 5, 2)->default(0)->after('pis_base');
            $table->decimal('pis_amount', 15, 2)->default(0)->after('pis_percent');

            $table->decimal('cofins_base', 15, 2)->default(0)->after('pis_amount');
            $table->decimal('cofins_percent', 5, 2)->default(0)->after('cofins_base');
            $table->decimal('cofins_amount', 15, 2)->default(0)->after('cofins_percent');

            $table->decimal('fcp_base', 15, 2)->default(0)->after('cofins_amount');
            $table->decimal('fcp_percent', 5, 2)->default(0)->after('fcp_base');
            $table->decimal('fcp_amount', 15, 2)->default(0)->after('fcp_percent');

            // Total do item
            $table->decimal('total', 15, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'ean',
                'description',
                'unit',
                'discount_percent',
                'addition',
                'addition_percent',
                'batch',
                'expiry_date',
                'serial_number',
                'stock_location',
                'cfop',
                'ncm',
                'cst',
                'csosn',
                'origin',
                'icms_base',
                'icms_percent',
                'icms_amount',
                'icms_st_base',
                'icms_st_percent',
                'icms_st_amount',
                'ipi_base',
                'ipi_percent',
                'ipi_amount',
                'pis_base',
                'pis_percent',
                'pis_amount',
                'cofins_base',
                'cofins_percent',
                'cofins_amount',
                'fcp_base',
                'fcp_percent',
                'fcp_amount',
                'total',
            ]);
        });
    }
};

