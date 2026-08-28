<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('received_quantity', 12, 3)->default(0);
        });
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->string('matching_status', 20)->nullable();
            $table->json('matching_divergences')->nullable();
            $table->foreignId('matching_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('matching_approved_at')->nullable();
            $table->timestamp('receipt_applied_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->dropConstrainedForeignId('matching_approved_by');
            $table->dropColumn(['matching_status', 'matching_divergences', 'matching_approved_at', 'receipt_applied_at']);
        });
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });
    }
};
