<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Remove old stub columns if they exist
            if (Schema::hasColumn('payrolls', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('payrolls', 'description')) {
                $table->dropColumn('description');
            }

            $table->string('employee_id')->nullable()->after('id');
            $table->date('reference_month')->nullable()->after('employee_id'); // stored as Y-m-01
            $table->decimal('base_salary', 15, 2)->default(0)->after('reference_month');
            $table->decimal('total_earnings', 15, 2)->default(0)->after('base_salary');
            $table->decimal('total_deductions', 15, 2)->default(0)->after('total_earnings');
            $table->decimal('net_salary', 15, 2)->default(0)->after('total_deductions');
            $table->string('status')->default('draft')->after('net_salary');
            $table->date('payment_date')->nullable()->after('status');
            $table->text('observations')->nullable()->after('payment_date');

            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn([
                'employee_id', 'reference_month', 'base_salary',
                'total_earnings', 'total_deductions', 'net_salary',
                'status', 'payment_date', 'observations',
            ]);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });
    }
};

