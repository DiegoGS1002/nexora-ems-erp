<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->string('cost_center')->nullable();
            $table->foreignId('chart_of_account_id')->nullable()->constrained('plans_of_accounts')->nullOnDelete();
            $table->string('financial_nature')->nullable();
            $table->json('installment_data')->nullable();
        });
        Schema::table('fiscal_invoice_items_in', function (Blueprint $table) {
            $table->json('tax_data')->nullable();
            $table->boolean('credit_allowed')->default(true);
            $table->decimal('allocated_shipping', 14, 2)->default(0);
            $table->decimal('allocated_insurance', 14, 2)->default(0);
            $table->decimal('allocated_other', 14, 2)->default(0);
        });
        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->foreignId('fiscal_invoice_in_id')->nullable()->constrained('fiscal_invoices_in')->nullOnDelete();
            $table->unsignedSmallInteger('installment_number')->nullable();
            $table->unique(['fiscal_invoice_in_id', 'installment_number']);
        });
        Schema::create('fiscal_entry_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_invoice_in_id')->constrained('fiscal_invoices_in')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('fiscal_invoice_items_in')->cascadeOnDelete();
            $table->json('taxes');
            $table->boolean('credit_allowed');
            $table->timestamp('posted_at');
            $table->timestamps();
            $table->unique('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_entry_records');
        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->dropUnique(['fiscal_invoice_in_id', 'installment_number']);
            $table->dropConstrainedForeignId('fiscal_invoice_in_id');
            $table->dropColumn('installment_number');
        });
        Schema::table('fiscal_invoice_items_in', function (Blueprint $table) {
            $table->dropColumn(['tax_data', 'credit_allowed', 'allocated_shipping', 'allocated_insurance', 'allocated_other']);
        });
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chart_of_account_id');
            $table->dropColumn(['cost_center', 'financial_nature', 'installment_data']);
        });
    }
};
