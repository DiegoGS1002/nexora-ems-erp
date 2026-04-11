<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_operacoes_fiscais', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('codigo', 20)->unique();          // Código interno (ex: VENDA-EST)
            $table->string('descricao', 255);                // Descrição legível
            $table->string('natureza_operacao', 100)->nullable(); // Texto para NF-e (ex: Venda de Mercadoria)
            $table->enum('tipo_movimento', ['entrada', 'saida'])->default('saida');
            $table->boolean('is_active')->default(true);

            // CFOP
            $table->string('cfop', 4)->nullable();           // Ex: 5102, 6102, 1202

            // ── ICMS ──────────────────────────────────────
            $table->string('icms_cst', 3)->nullable();       // CST ou CSOSN (ex: 00, 102, 500)
            $table->tinyInteger('icms_modalidade_bc')->nullable(); // 0=Trans.Marg / 1=Pauta / 2=PMax / 3=Valor Op.
            $table->decimal('icms_aliquota', 5, 2)->nullable();    // %
            $table->decimal('icms_reducao_bc', 5, 2)->nullable();  // % Redução da Base de Cálculo

            // ── IPI ───────────────────────────────────────
            $table->string('ipi_cst', 2)->nullable();        // CST IPI (ex: 00, 50, 99)
            $table->string('ipi_modalidade', 20)->nullable(); // aliquota | pauta | unidade | isento
            $table->decimal('ipi_aliquota', 5, 2)->nullable();

            // ── PIS ───────────────────────────────────────
            $table->string('pis_cst', 2)->nullable();        // CST PIS (ex: 01, 07, 99)
            $table->decimal('pis_aliquota', 5, 2)->nullable();

            // ── COFINS ────────────────────────────────────
            $table->string('cofins_cst', 2)->nullable();     // CST COFINS
            $table->decimal('cofins_aliquota', 5, 2)->nullable();

            // Observações
            $table->text('observacoes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['tipo_movimento', 'is_active']);
            $table->index('cfop');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_operacoes_fiscais');
    }
};

