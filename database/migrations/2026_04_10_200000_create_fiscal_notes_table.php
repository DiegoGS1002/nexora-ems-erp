<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_notes', function (Blueprint $table) {
            $table->id();

            // Vínculo opcional com cliente
            $table->string('client_id')->nullable();
            $table->string('client_name')->nullable();       // Nome/razão social do destinatário

            // Dados fiscais
            $table->string('invoice_number', 9);             // Número da NF-e (até 9 dígitos)
            $table->string('series', 3)->default('1');       // Série
            $table->string('access_key', 44)->nullable()->unique(); // Chave de 44 dígitos
            $table->enum('type', ['nfe', 'nfce'])->default('nfe');
            $table->enum('environment', ['production', 'homologation'])->default('homologation');
            $table->enum('status', ['draft', 'sent', 'authorized', 'rejected', 'cancelled', 'denied'])->default('draft');

            // Autorização SEFAZ
            $table->string('protocol')->nullable();          // Número do protocolo de autorização
            $table->text('sefaz_message')->nullable();       // Mensagem de retorno/erro da SEFAZ
            $table->timestamp('authorized_at')->nullable();  // Data/hora de autorização

            // Cancelamento
            $table->string('cancel_protocol')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Armazenamento
            $table->string('xml_path')->nullable();          // Caminho do XML no storage
            $table->string('xml_cancel_path')->nullable();

            // Valores
            $table->decimal('amount', 14, 2)->default(0);   // Valor total da nota

            // Campos de controle
            $table->text('notes')->nullable();               // Observações internas
            $table->unsignedBigInteger('emitted_by')->nullable(); // Usuário que emitiu

            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->nullOnDelete();

            $table->foreign('emitted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['status', 'created_at']);
            $table->index(['environment', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_notes');
    }
};

