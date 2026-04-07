<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('baccarat_accounts', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('name');
            $table->string('agency')->nullable()->after('bank_name');
            $table->string('number')->nullable()->after('agency');
            $table->enum('type', ['corrente', 'poupanca', 'caixa_interno', 'digital'])
                  ->default('corrente')->after('number');
            $table->decimal('balance', 15, 2)->default(0)->after('type');
            $table->decimal('predicted_balance', 15, 2)->default(0)->after('balance');
            $table->string('color', 20)->nullable()->after('predicted_balance'); // hex color for card gradient
            $table->unsignedBigInteger('chart_of_account_id')->nullable()->after('color');
            $table->boolean('is_active')->default(true)->after('chart_of_account_id');
            $table->boolean('is_reconciled')->default(false)->after('is_active');
            $table->date('last_reconciled_at')->nullable()->after('is_reconciled');

            $table->foreign('chart_of_account_id')
                  ->references('id')
                  ->on('plans_of_accounts')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('baccarat_accounts', function (Blueprint $table) {
            $table->dropForeign(['chart_of_account_id']);
            $table->dropColumn([
                'bank_name', 'agency', 'number', 'type', 'balance',
                'predicted_balance', 'color', 'chart_of_account_id',
                'is_active', 'is_reconciled', 'last_reconciled_at',
            ]);
        });
    }
};

