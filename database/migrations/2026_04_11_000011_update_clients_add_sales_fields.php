<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Campos para controle de crédito e vendas
            $table->string('inscricao_estadual')->nullable()->after('taxNumber');
            $table->decimal('credit_limit', 15, 2)->nullable()->after('inscricao_estadual');
            $table->string('payment_condition_default')->nullable()->after('credit_limit');
            $table->enum('situation', ['active', 'inactive', 'defaulter'])->default('active')->after('payment_condition_default');
            $table->foreignId('price_table_id')->nullable()->after('situation');
            $table->decimal('discount_limit', 5, 2)->nullable()->after('price_table_id');

            // Foreign key
            $table->foreign('price_table_id')->references('id')->on('price_tables')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['price_table_id']);
            $table->dropColumn([
                'inscricao_estadual',
                'credit_limit',
                'payment_condition_default',
                'situation',
                'price_table_id',
                'discount_limit',
            ]);
        });
    }
};

