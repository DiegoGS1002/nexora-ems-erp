<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('component_id');
            $table->decimal('required_qty', 15, 3);
            $table->decimal('consumed_qty', 15, 3)->default(0);
            $table->timestamps();

            $table->foreign('production_order_id')
                  ->references('id')->on('production_orders')
                  ->onDelete('cascade');

            $table->foreign('component_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};

