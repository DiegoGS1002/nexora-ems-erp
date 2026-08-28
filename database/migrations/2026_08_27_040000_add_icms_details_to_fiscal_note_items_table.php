<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->json('icms_data')->nullable();
            $table->string('tax_benefit_code', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->dropColumn(['icms_data', 'tax_benefit_code']);
        });
    }
};
