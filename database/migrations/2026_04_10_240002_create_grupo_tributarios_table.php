<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_tributarios', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique();          // Ex: GT-MERCH-SN
            $table->string('nome', 150);
            $table->text('descricao')->nullable();

            // Regime tributário da empresa
            $table->enum('regime_tributario', [
                'simples_nacional',
                'lucro_presumido',
                'lucro_real',
                'todos',
            ])->default('todos');

            // NCM padrão para produtos deste grupo
            $table->string('ncm', 8)->nullable();            // 8 dígitos

            // Operações fiscais vinculadas (saída e entrada)
            $table->foreignId('tipo_operacao_saida_id')
                ->nullable()
                ->constrained('tipo_operacoes_fiscais')
                ->nullOnDelete();

            $table->foreignId('tipo_operacao_entrada_id')
                ->nullable()
                ->constrained('tipo_operacoes_fiscais')
                ->nullOnDelete();

            // ── ICMS ────────────────────────────────────────────────
            $table->string('icms_cst', 3)->nullable();
            $table->tinyInteger('icms_modalidade_bc')->nullable(); // 0–3
            $table->decimal('icms_aliquota', 5, 2)->nullable();
            $table->decimal('icms_reducao_bc', 5, 2)->nullable();

            // ── IPI ─────────────────────────────────────────────────
            $table->string('ipi_cst', 2)->nullable();
            $table->string('ipi_modalidade', 20)->nullable();
            $table->decimal('ipi_aliquota', 5, 2)->nullable();

            // ── PIS ─────────────────────────────────────────────────
            $table->string('pis_cst', 2)->nullable();
            $table->decimal('pis_aliquota', 5, 2)->nullable();

            // ── COFINS ──────────────────────────────────────────────
            $table->string('cofins_cst', 2)->nullable();
            $table->decimal('cofins_aliquota', 5, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['regime_tributario', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_tributarios');
    }
};

