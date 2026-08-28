<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('fiscal_note_id')->nullable()->constrained()->nullOnDelete();
            $table->string('operation', 50);
            $table->json('payload')->nullable();
            $table->json('result')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['company_id', 'operation', 'occurred_at']);
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->string('certificate_path')->nullable();
            $table->timestamp('certificate_expires_at')->nullable();
            $table->unsignedSmallInteger('xml_retention_years')->default(11);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['certificate_path', 'certificate_expires_at', 'xml_retention_years']);
        });
        Schema::dropIfExists('fiscal_operation_logs');
    }
};
