<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('product_category_id')->nullable()->after('category');
            $table->uuid('unit_of_measure_id')->nullable()->after('unit_of_measure');

            $table->foreign('product_category_id')
                ->references('id')
                ->on('product_categories')
                ->nullOnDelete();

            $table->foreign('unit_of_measure_id')
                ->references('id')
                ->on('units_of_measure')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropForeign(['unit_of_measure_id']);
            $table->dropColumn(['product_category_id', 'unit_of_measure_id']);
        });
    }
};

