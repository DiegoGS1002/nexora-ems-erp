<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_note_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_note_id')->constrained('fiscal_notes')->cascadeOnDelete();
            $table->string('event_type', 30);
            $table->text('description');
            $table->string('protocol')->nullable();
            $table->json('response_data')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fiscal_note_id', 'created_at']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_note_events');
    }
};
