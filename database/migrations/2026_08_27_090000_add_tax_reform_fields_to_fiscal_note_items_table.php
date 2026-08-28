<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->json('ibs_cbs_data')->nullable();
            $table->json('selective_tax_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->dropColumn(['ibs_cbs_data', 'selective_tax_data']);
        });
    }
};
