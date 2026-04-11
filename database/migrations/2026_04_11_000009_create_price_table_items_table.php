<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_table_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_table_id')->constrained('price_tables')->cascadeOnDelete();
            $table->string('product_id');

            $table->decimal('price', 15, 2);
            $table->decimal('minimum_price', 15, 2)->nullable();
            $table->decimal('promotional_price', 15, 2)->nullable();
            $table->date('promotional_valid_from')->nullable();
            $table->date('promotional_valid_until')->nullable();

            // Preço por quantidade
            $table->decimal('quantity_from', 15, 3)->nullable();
            $table->decimal('quantity_to', 15, 3)->nullable();
            $table->decimal('quantity_price', 15, 2)->nullable();

            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->unique(['price_table_id', 'product_id', 'quantity_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_table_items');
    }
};

