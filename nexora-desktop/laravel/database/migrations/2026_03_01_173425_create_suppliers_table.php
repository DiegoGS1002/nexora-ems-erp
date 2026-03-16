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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('social_name');
            $table->string('taxNumber');
            $table->string('email');
            $table->string('address_zip_code');
            $table->string('address_street');
            $table->string('address_number');
            $table->string('address_complement');
            $table->string('address_district');
            $table->string('address_city');
            $table->string('address_state', 2);
            $table->string('phone_number');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
