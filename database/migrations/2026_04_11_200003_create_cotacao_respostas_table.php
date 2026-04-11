<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotacao_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotacao_id')->constrained('cotacoes')->cascadeOnDelete();
            $table->foreignId('cotacao_item_id')->constrained('cotacao_items')->cascadeOnDelete();

            // Supplier UUID FK
            $table->string('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();

            $table->decimal('unit_price', 14, 2)->default(0);
            $table->integer('delivery_days')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('selected')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotacao_respostas');
    }
};

