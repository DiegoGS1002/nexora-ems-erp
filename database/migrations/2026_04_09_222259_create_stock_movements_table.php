<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->char('product_id', 36);
            $table->unsignedBigInteger('user_id');
            $table->decimal('quantity', 15, 3);
            $table->enum('type', ['input', 'output', 'adjustment', 'transfer']);
            $table->string('origin');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
