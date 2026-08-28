<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dfe_distribution_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('environment', 20);
            $table->unsignedBigInteger('last_nsu')->default(0);
            $table->unsignedBigInteger('max_nsu')->default(0);
            $table->timestamp('next_query_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'environment']);
        });

        Schema::create('dfe_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('environment', 20);
            $table->unsignedBigInteger('nsu');
            $table->string('access_key', 44)->nullable();
            $table->string('schema', 100);
            $table->string('document_type', 30);
            $table->string('status', 30)->default('received');
            $table->string('xml_path')->nullable();
            $table->longText('raw_xml');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'environment', 'nsu']);
            $table->index(['access_key', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dfe_documents');
        Schema::dropIfExists('dfe_distribution_states');
    }
};
