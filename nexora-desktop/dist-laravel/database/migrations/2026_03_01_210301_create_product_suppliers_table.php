<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('supplier_id');

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();

            $table->unique(['product_id', 'supplier_id']);
            $table->timestamps(); // opcional, mas recomendado
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
