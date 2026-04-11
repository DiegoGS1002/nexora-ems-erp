<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->string('product_id')->nullable()->after('description');
            $table->decimal('target_quantity', 15, 3)->default(1)->after('product_id');
            $table->decimal('produced_quantity', 15, 3)->default(0)->after('target_quantity');
            $table->dateTime('start_date')->nullable()->after('produced_quantity');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->string('status')->default('planned')->after('end_date');
            $table->decimal('estimated_cost', 15, 2)->default(0)->after('status');
            $table->string('lot_number')->nullable()->after('estimated_cost');
            $table->text('notes')->nullable()->after('lot_number');
            $table->unsignedBigInteger('user_id')->nullable()->after('notes');

            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'product_id', 'target_quantity', 'produced_quantity',
                'start_date', 'end_date', 'status', 'estimated_cost',
                'lot_number', 'notes', 'user_id',
            ]);
        });
    }
};

