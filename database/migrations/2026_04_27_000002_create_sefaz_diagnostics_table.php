<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Banco de diagnósticos SEFAZ estruturado.
     * Consultado ANTES do RAG para rejeições exatas — resposta determinística.
     */
    public function up(): void
    {
        Schema::create('sefaz_diagnostics', function (Blueprint $table) {
            $table->id();
            $table->string('rejection_code', 10)->unique()->index();
            $table->string('titulo');
            $table->text('causa');
            $table->text('solucao');
            $table->string('module')->nullable();           // fiscal | cadastro | configuracao
            $table->string('severity')->default('error');  // error | warning | info
            $table->json('related_codes')->nullable();      // códigos relacionados
            $table->json('tags')->nullable();               // tags para busca: ncm, cfop, cnpj, ie...
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sefaz_diagnostics');
    }
};

