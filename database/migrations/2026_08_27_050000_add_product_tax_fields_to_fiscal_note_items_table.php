<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->string('cest', 7)->nullable();
            $table->string('ex_tipi', 3)->nullable();
            $table->string('tax_ean', 14)->nullable();
            $table->string('tax_unit', 6)->nullable();
            $table->decimal('tax_quantity', 15, 4)->nullable();
            $table->decimal('tax_unit_price', 21, 10)->nullable();
            $table->boolean('relevant_scale')->nullable();
            $table->string('manufacturer_cnpj', 14)->nullable();
            $table->string('purchase_order_number', 15)->nullable();
            $table->unsignedInteger('purchase_order_item')->nullable();
            $table->json('traceability')->nullable();
            $table->json('medicine_data')->nullable();
            $table->json('fuel_data')->nullable();
            $table->json('vehicle_data')->nullable();
            $table->json('weapon_data')->nullable();
            $table->json('import_declarations')->nullable();
            $table->json('dfe_reference')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->dropColumn([
                'cest', 'ex_tipi', 'tax_ean', 'tax_unit', 'tax_quantity',
                'tax_unit_price', 'relevant_scale', 'manufacturer_cnpj',
                'purchase_order_number', 'purchase_order_item', 'traceability',
                'medicine_data', 'fuel_data', 'vehicle_data', 'weapon_data',
                'import_declarations', 'dfe_reference',
            ]);
        });
    }
};
