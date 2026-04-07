<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans_of_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->string('code', 30)->nullable()->after('parent_id');
            $table->enum('type', ['receita', 'despesa', 'ativo', 'passivo'])->nullable()->after('code');
            $table->boolean('is_selectable')->default(true)->after('type');
            $table->boolean('is_active')->default(true)->after('is_selectable');

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('plans_of_accounts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'code', 'type', 'is_selectable', 'is_active']);
        });
    }
};
