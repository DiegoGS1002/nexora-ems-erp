<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->string('authorization_protocol')->nullable();
            $table->string('authorization_status', 5)->nullable();
            $table->string('original_xml_hash', 64)->nullable();
            $table->timestamp('xml_validated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->dropColumn(['authorization_protocol', 'authorization_status', 'original_xml_hash', 'xml_validated_at']);
        });
    }
};
