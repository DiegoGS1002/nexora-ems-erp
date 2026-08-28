<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->unique(
                ['company_id', 'type', 'series', 'environment', 'invoice_number'],
                'fiscal_notes_company_number_unique'
            );
        });

        Schema::create('fiscal_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 10);
            $table->string('series', 3);
            $table->string('environment', 20);
            $table->unsignedBigInteger('next_number');
            $table->timestamps();

            $table->unique(
                ['company_id', 'document_type', 'series', 'environment'],
                'fiscal_number_sequences_scope_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_number_sequences');

        Schema::table('fiscal_notes', function (Blueprint $table) {
            $table->dropUnique('fiscal_notes_company_number_unique');
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
