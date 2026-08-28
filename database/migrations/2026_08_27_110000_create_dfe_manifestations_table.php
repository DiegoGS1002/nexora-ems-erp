<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dfe_manifestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dfe_document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('type');
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->string('justification', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('protocol')->nullable();
            $table->string('sefaz_code', 5)->nullable();
            $table->text('sefaz_message')->nullable();
            $table->longText('response_xml')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->unique(['dfe_document_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dfe_manifestations');
    }
};
