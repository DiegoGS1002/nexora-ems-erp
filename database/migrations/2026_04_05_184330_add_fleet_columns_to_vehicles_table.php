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
        Schema::table('vehicles', function (Blueprint $table) {
            $add = [
                'vehicle_type'       => fn () => $table->string('vehicle_type')->nullable(),
                'category'           => fn () => $table->string('category')->nullable(),
                'species'            => fn () => $table->string('species')->nullable(),
                'manufacturing_year' => fn () => $table->string('manufacturing_year')->nullable(),
                'model_year'         => fn () => $table->string('model_year')->nullable(),
                'power_hp'           => fn () => $table->string('power_hp')->nullable(),
                'displacement_cc'    => fn () => $table->string('displacement_cc')->nullable(),
                'doors'              => fn () => $table->unsignedTinyInteger('doors')->nullable(),
                'passenger_capacity' => fn () => $table->unsignedSmallInteger('passenger_capacity')->nullable(),
                'transmission_type'  => fn () => $table->string('transmission_type')->nullable(),
                'traction_type'      => fn () => $table->string('traction_type')->nullable(),
                'gross_weight'       => fn () => $table->decimal('gross_weight', 10, 2)->nullable(),
                'net_weight'         => fn () => $table->decimal('net_weight', 10, 2)->nullable(),
                'cargo_capacity'     => fn () => $table->decimal('cargo_capacity', 10, 2)->nullable(),
                'department'         => fn () => $table->string('department')->nullable(),
                'responsible_driver' => fn () => $table->string('responsible_driver')->nullable(),
                'cost_center'        => fn () => $table->string('cost_center')->nullable(),
                'unit'               => fn () => $table->string('unit')->nullable(),
                'current_location'   => fn () => $table->string('current_location')->nullable(),
                'location_note'      => fn () => $table->string('location_note')->nullable(),
                'is_active'          => fn () => $table->boolean('is_active')->default(true),
                'acquisition_date'   => fn () => $table->date('acquisition_date')->nullable(),
                'acquisition_value'  => fn () => $table->decimal('acquisition_value', 15, 2)->nullable(),
                'photos'             => fn () => $table->json('photos')->nullable(),
                'observations'       => fn () => $table->text('observations')->nullable(),
            ];

            foreach ($add as $column => $definition) {
                if (! Schema::hasColumn('vehicles', $column)) {
                    $definition();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = [
                'vehicle_type', 'category', 'species', 'manufacturing_year', 'model_year',
                'power_hp', 'displacement_cc', 'doors', 'passenger_capacity',
                'transmission_type', 'traction_type', 'gross_weight', 'net_weight',
                'cargo_capacity', 'department', 'responsible_driver', 'cost_center',
                'unit', 'current_location', 'location_note', 'is_active',
                'acquisition_date', 'acquisition_value', 'photos', 'observations',
            ];
            foreach (array_filter($columns, fn ($c) => Schema::hasColumn('vehicles', $c)) as $col) {
                $table->dropColumn($col);
            }
        });
    }
};
