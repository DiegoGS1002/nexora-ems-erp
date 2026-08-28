<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->decimal('import_tax_amount', 14, 2)->default(0);
            $table->decimal('returned_ipi_amount', 14, 2)->default(0);
            $table->decimal('approximate_tax_amount', 14, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_note_items', function (Blueprint $table) {
            $table->dropColumn(['import_tax_amount', 'returned_ipi_amount', 'approximate_tax_amount']);
        });
    }
};
