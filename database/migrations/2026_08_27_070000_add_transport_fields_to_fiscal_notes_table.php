<?php

use App\Enums\FiscalFreightMode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->unsignedTinyInteger('freight_mode')->default(FiscalFreightMode::None->value);
            $table->json('transport_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->dropColumn(['freight_mode', 'transport_data']);
        });
    }
};
