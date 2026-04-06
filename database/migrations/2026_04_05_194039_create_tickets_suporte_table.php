<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusTicketSuporte;
use App\Enums\PrioridadeTicketSuporte;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_suporte', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('assunto');
            $table->string('status')->default(StatusTicketSuporte::Aberto->value);
            $table->string('prioridade')->default(PrioridadeTicketSuporte::Media->value);
            $table->string('categoria')->nullable();
            $table->timestamp('fechado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_suporte');
    }
};
