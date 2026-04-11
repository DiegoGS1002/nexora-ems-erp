<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('product_id');
            $table->decimal('target_quantity', 15, 3);
            $table->decimal('produced_quantity', 15, 3)->default(0);
            $table->timestamps();

            $table->foreign('production_order_id')
                  ->references('id')->on('production_orders')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_order_products');
    }
};

