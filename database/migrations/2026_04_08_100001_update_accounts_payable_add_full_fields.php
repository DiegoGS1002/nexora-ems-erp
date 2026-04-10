<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->string('description_title')->nullable()->after('id');
            $table->string('supplier_id')->nullable()->after('description_title');
            $table->unsignedBigInteger('chart_of_account_id')->nullable()->after('supplier_id');
            $table->decimal('amount', 15, 2)->default(0)->after('chart_of_account_id');
            $table->date('due_date_at')->nullable()->after('amount');
            $table->date('payment_date')->nullable()->after('due_date_at');
            $table->decimal('paid_amount', 15, 2)->nullable()->after('payment_date');
            $table->string('status')->default('pending')->after('paid_amount');
            $table->text('observation')->nullable()->after('status');
            $table->string('attachment_path')->nullable()->after('observation');
            $table->boolean('is_recurring')->default(false)->after('attachment_path');
            $table->unsignedTinyInteger('recurrence_day')->nullable()->after('is_recurring');

            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('chart_of_account_id')->references('id')->on('plans_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounts_payable', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['chart_of_account_id']);
            $table->dropColumn([
                'description_title', 'supplier_id', 'chart_of_account_id', 'amount',
                'due_date_at', 'payment_date', 'paid_amount', 'status',
                'observation', 'attachment_path', 'is_recurring', 'recurrence_day',
            ]);
        });
    }
};

