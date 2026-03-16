<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('name')->unique();
            $table->string('ean')->unique();
            $table->text('description');
            $table->string('unit_of_measure')->default('unidade');
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->date('expiration_date')->nullable();
            $table->string('category');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
