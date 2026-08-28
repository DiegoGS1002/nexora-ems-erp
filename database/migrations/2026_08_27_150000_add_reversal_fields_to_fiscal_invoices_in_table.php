<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->timestamp('reversed_at')->nullable();
            $table->string('cancellation_event_protocol')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_invoices_in', function (Blueprint $table) {
            $table->dropColumn(['reversed_at', 'cancellation_event_protocol']);
        });
    }
};
