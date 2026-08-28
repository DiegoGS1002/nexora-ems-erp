<?php

use App\Enums\FiscalConsumer;
use App\Enums\FiscalEmissionType;
use App\Enums\FiscalOperationType;
use App\Enums\FiscalPresence;
use App\Enums\FiscalPurpose;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->string('operation_nature', 60)->default('Venda de Mercadoria');
            $table->string('operation_type', 30)->default(FiscalOperationType::Sale->value);
            $table->unsignedTinyInteger('destination')->nullable();
            $table->unsignedTinyInteger('consumer_type')->default(FiscalConsumer::Final->value);
            $table->unsignedTinyInteger('presence_type')->default(FiscalPresence::InPerson->value);
            $table->unsignedTinyInteger('purpose')->default(FiscalPurpose::Normal->value);
            $table->unsignedTinyInteger('emission_type')->default(FiscalEmissionType::Normal->value);
            $table->string('referenced_access_key', 44)->nullable();
            $table->string('intermediary_cnpj', 14)->nullable();
            $table->string('intermediary_identifier', 60)->nullable();
            $table->json('delivery_location')->nullable();
            $table->json('pickup_location')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->dropColumn([
                'operation_nature',
                'operation_type',
                'destination',
                'consumer_type',
                'presence_type',
                'purpose',
                'emission_type',
                'referenced_access_key',
                'intermediary_cnpj',
                'intermediary_identifier',
                'delivery_location',
                'pickup_location',
            ]);
        });
    }
};
