<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReceivableStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            // Drop legacy placeholder columns
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('accounts_receivable', function (Blueprint $table) {
            $table->string('description_title')->after('id');
            $table->string('client_id')->nullable()->after('description_title');
            $table->unsignedBigInteger('chart_of_account_id')->nullable()->after('client_id');
            $table->decimal('amount', 15, 2)->after('chart_of_account_id');
            $table->decimal('received_amount', 15, 2)->default(0)->after('amount');
            $table->date('due_date_at')->after('received_amount');
            $table->date('received_at')->nullable()->after('due_date_at');
            $table->string('payment_method')->nullable()->after('received_at');
            $table->integer('installment_number')->default(1)->after('payment_method');
            $table->string('status')->default(ReceivableStatus::Pending->value)->after('installment_number');
            $table->text('observation')->nullable()->after('status');

            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            $table->foreign('chart_of_account_id')->references('id')->on('plans_of_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_receivable', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['chart_of_account_id']);
            $table->dropColumn([
                'description_title', 'client_id', 'chart_of_account_id',
                'amount', 'received_amount', 'due_date_at', 'received_at',
                'payment_method', 'installment_number', 'status', 'observation',
            ]);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });
    }
};
