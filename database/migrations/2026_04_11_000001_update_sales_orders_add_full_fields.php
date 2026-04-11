<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // 1. Cabeçalho - Identificação e controle
            $table->timestamp('order_date')->nullable()->after('order_number');
            $table->timestamp('expected_delivery_date')->nullable()->after('delivery_date');
            $table->string('operation_type')->nullable()->after('is_fiscal'); // Tipo de operação
            $table->string('sales_channel')->nullable()->after('operation_type'); // Canal de venda
            $table->string('origin')->nullable()->after('sales_channel'); // Origem do pedido
            $table->string('company_branch')->nullable()->after('origin'); // Empresa/filial
            $table->unsignedBigInteger('seller_id')->nullable()->after('user_id'); // Vendedor/representante

            // 2. Dados do Cliente (campos adicionais além do FK)
            $table->string('client_cpf_cnpj')->nullable()->after('client_id');
            $table->string('client_ie')->nullable()->after('client_cpf_cnpj'); // Inscrição Estadual
            $table->string('client_type')->nullable()->after('client_ie'); // PF/PJ
            $table->decimal('client_credit_limit', 15, 2)->nullable()->after('client_type');
            $table->string('client_situation')->nullable()->after('client_credit_limit'); // ativo/inadimplente
            $table->string('client_contact_phone')->nullable()->after('client_situation');
            $table->string('client_contact_email')->nullable()->after('client_contact_phone');

            // 5. Tabela de Preço
            $table->unsignedBigInteger('price_table_id')->nullable()->after('payment_condition');
            $table->decimal('minimum_margin', 5, 2)->nullable()->after('price_table_id');

            // 6. Impostos - Totais
            $table->decimal('icms_base', 15, 2)->default(0)->after('tax_amount');
            $table->decimal('icms_amount', 15, 2)->default(0)->after('icms_base');
            $table->decimal('icms_st_amount', 15, 2)->default(0)->after('icms_amount');
            $table->decimal('ipi_amount', 15, 2)->default(0)->after('icms_st_amount');
            $table->decimal('pis_amount', 15, 2)->default(0)->after('ipi_amount');
            $table->decimal('cofins_amount', 15, 2)->default(0)->after('pis_amount');
            $table->decimal('fcp_amount', 15, 2)->default(0)->after('cofins_amount');

            // 8. Totais do Pedido
            $table->decimal('gross_total', 15, 2)->default(0)->after('subtotal');
            $table->decimal('additions_amount', 15, 2)->default(0)->after('discount_amount');
            $table->decimal('insurance_amount', 15, 2)->default(0)->after('shipping_amount');
            $table->decimal('other_expenses', 15, 2)->default(0)->after('insurance_amount');
            $table->decimal('net_total', 15, 2)->default(0)->after('other_expenses');

            // 9. Logística / Entrega
            $table->unsignedBigInteger('carrier_id')->nullable()->after('expected_delivery_date');
            $table->string('freight_type')->nullable()->after('carrier_id'); // CIF/FOB
            $table->decimal('gross_weight', 10, 3)->nullable()->after('freight_type');
            $table->decimal('net_weight', 10, 3)->nullable()->after('gross_weight');
            $table->integer('volumes')->nullable()->after('net_weight');
            $table->string('tracking_code')->nullable()->after('volumes');
            $table->text('delivery_notes')->nullable()->after('tracking_code');

            // 10. Separação / Romaneio
            $table->string('separation_status')->nullable()->after('status');
            $table->unsignedBigInteger('separator_user_id')->nullable()->after('separation_status');
            $table->timestamp('separation_date')->nullable()->after('separator_user_id');
            $table->timestamp('conference_date')->nullable()->after('separation_date');

            // 11. Faturamento / Nota Fiscal
            $table->unsignedBigInteger('fiscal_note_id')->nullable()->after('conference_date');
            $table->string('nfe_number')->nullable()->after('fiscal_note_id');
            $table->string('nfe_series')->nullable()->after('nfe_number');
            $table->string('nfe_key')->nullable()->after('nfe_series');
            $table->string('nfe_protocol')->nullable()->after('nfe_key');
            $table->string('nfe_status')->nullable()->after('nfe_protocol');
            $table->timestamp('nfe_issued_at')->nullable()->after('nfe_status');

            // 17. Observações
            $table->text('fiscal_notes_obs')->nullable()->after('customer_notes'); // Vai para NF-e

            // 16. Aprovações
            $table->unsignedBigInteger('approved_by')->nullable()->after('nfe_issued_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_reason')->nullable()->after('approved_at');
            $table->boolean('needs_approval')->default(false)->after('approval_reason');

            // 18. Auditoria
            $table->unsignedBigInteger('created_by')->nullable()->after('needs_approval');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Foreign Keys
            $table->foreign('seller_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('separator_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['separator_user_id']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'order_date',
                'expected_delivery_date',
                'operation_type',
                'sales_channel',
                'origin',
                'company_branch',
                'seller_id',
                'client_cpf_cnpj',
                'client_ie',
                'client_type',
                'client_credit_limit',
                'client_situation',
                'client_contact_phone',
                'client_contact_email',
                'price_table_id',
                'minimum_margin',
                'icms_base',
                'icms_amount',
                'icms_st_amount',
                'ipi_amount',
                'pis_amount',
                'cofins_amount',
                'fcp_amount',
                'gross_total',
                'additions_amount',
                'insurance_amount',
                'other_expenses',
                'net_total',
                'carrier_id',
                'freight_type',
                'gross_weight',
                'net_weight',
                'volumes',
                'tracking_code',
                'delivery_notes',
                'separation_status',
                'separator_user_id',
                'separation_date',
                'conference_date',
                'fiscal_note_id',
                'nfe_number',
                'nfe_series',
                'nfe_key',
                'nfe_protocol',
                'nfe_status',
                'nfe_issued_at',
                'fiscal_notes_obs',
                'approved_by',
                'approved_at',
                'approval_reason',
                'needs_approval',
                'created_by',
                'updated_by',
            ]);
        });
    }
};

