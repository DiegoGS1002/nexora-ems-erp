<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('plate')->unique();
            $table->string('renavam')->unique();
            $table->string('chassis')->unique();
            $table->string('vehicle_type')->nullable();
            $table->string('category')->nullable();
            $table->string('species')->nullable();
            $table->string('manufacturing_year')->nullable();
            $table->string('model_year')->nullable();
            $table->string('brand');
            $table->string('model');
            $table->string('color');
            $table->string('fuel_type');
            $table->string('year');
            $table->string('power_hp')->nullable();
            $table->string('displacement_cc')->nullable();
            $table->unsignedTinyInteger('doors')->nullable();
            $table->unsignedSmallInteger('passenger_capacity')->nullable();
            $table->string('transmission_type')->nullable();
            $table->string('traction_type')->nullable();
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->decimal('net_weight', 10, 2)->nullable();
            $table->decimal('cargo_capacity', 10, 2)->nullable();
            $table->string('department')->nullable();
            $table->string('responsible_driver')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('unit')->nullable();
            $table->string('current_location')->nullable();
            $table->string('location_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_value', 15, 2)->nullable();
            $table->json('photos')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
