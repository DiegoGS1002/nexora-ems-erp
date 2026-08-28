<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_reference_codes', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('code', 30);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->unique(['type', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_reference_codes');
    }
};
