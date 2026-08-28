<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->json('payment_details')->nullable();
            $table->decimal('change_amount', 14, 2)->default(0);
            $table->json('invoice_data')->nullable();
            $table->json('installment_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->dropColumn(['payment_details', 'change_amount', 'invoice_data', 'installment_data']);
        });
    }
};
