<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ncm', 8)->nullable()->after('ean');
            $table->string('cfop_saida', 4)->nullable()->after('ncm');
            $table->string('cfop_entrada', 4)->nullable()->after('cfop_saida');
            $table->foreignId('grupo_tributario_id')
                ->nullable()
                ->after('cfop_entrada')
                ->constrained('grupo_tributarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['grupo_tributario_id']);
            $table->dropColumn(['ncm', 'cfop_saida', 'cfop_entrada', 'grupo_tributario_id']);
        });
    }
};

