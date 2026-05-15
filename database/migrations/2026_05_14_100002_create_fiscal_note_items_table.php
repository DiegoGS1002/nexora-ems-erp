<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_note_id')->constrained('fiscal_notes')->cascadeOnDelete();
            $table->unsignedSmallInteger('item_number');

            // Produto
            $table->string('product_code', 60)->nullable();
            $table->string('ean', 14)->nullable();
            $table->string('description', 120);
            $table->string('ncm', 8)->nullable();
            $table->string('cfop', 4)->nullable();
            $table->string('unit', 6)->default('UN');
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 21, 10);
            $table->decimal('total', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('freight', 14, 2)->default(0);
            $table->decimal('insurance', 14, 2)->default(0);
            $table->decimal('other', 14, 2)->default(0);

            // Origem / CST
            $table->tinyInteger('origin')->default(0);
            $table->string('cst', 3)->nullable();
            $table->string('csosn', 4)->nullable();

            // ICMS
            $table->decimal('icms_base', 14, 2)->default(0);
            $table->decimal('icms_percent', 5, 2)->default(0);
            $table->decimal('icms_amount', 14, 2)->default(0);
            $table->tinyInteger('icms_mod_bc')->nullable();

            // ICMS ST
            $table->decimal('icms_st_base', 14, 2)->default(0);
            $table->decimal('icms_st_percent', 5, 2)->default(0);
            $table->decimal('icms_st_amount', 14, 2)->default(0);

            // IPI
            $table->string('ipi_cst', 2)->nullable();
            $table->decimal('ipi_base', 14, 2)->default(0);
            $table->decimal('ipi_percent', 5, 2)->default(0);
            $table->decimal('ipi_amount', 14, 2)->default(0);

            // PIS
            $table->string('pis_cst', 2)->nullable();
            $table->decimal('pis_base', 14, 2)->default(0);
            $table->decimal('pis_percent', 5, 2)->default(0);
            $table->decimal('pis_amount', 14, 2)->default(0);

            // COFINS
            $table->string('cofins_cst', 2)->nullable();
            $table->decimal('cofins_base', 14, 2)->default(0);
            $table->decimal('cofins_percent', 5, 2)->default(0);
            $table->decimal('cofins_amount', 14, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_note_items');
    }
};

