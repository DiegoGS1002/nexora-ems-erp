<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->json('nfe_payload')->nullable()->after('notes');
            $table->string('ibge_municipio', 7)->nullable()->after('nfe_payload');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->integer('crt')->default(1)->after('inscricao_municipal');
            $table->string('cnae', 10)->nullable()->after('crt');
            $table->string('ibge_municipio', 7)->nullable()->after('cnae');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('ibge_municipio', 7)->nullable()->after('address_state');
            $table->tinyInteger('ind_ie_dest')->default(9)->after('ibge_municipio');
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->dropColumn(['nfe_payload', 'ibge_municipio']);
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['crt', 'cnae', 'ibge_municipio']);
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['ibge_municipio', 'ind_ie_dest']);
        });
    }
};

