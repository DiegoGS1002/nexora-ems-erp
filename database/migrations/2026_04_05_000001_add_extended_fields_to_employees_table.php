<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('social_name')->nullable()->after('name');
            $table->string('rg')->nullable()->after('identification_number');
            $table->string('rg_issuer')->nullable()->after('rg');
            $table->date('rg_date')->nullable()->after('rg_issuer');
            $table->date('birth_date')->nullable()->after('rg_date');
            $table->string('gender')->nullable()->after('birth_date');
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('nationality')->nullable()->default('Brasileiro(a)')->after('marital_status');
            $table->string('birthplace')->nullable()->after('nationality');
            $table->string('phone_secondary')->nullable()->after('phone_number');
            // Address fields
            $table->string('zip_code', 9)->nullable()->after('address');
            $table->string('street')->nullable()->after('zip_code');
            $table->string('number', 20)->nullable()->after('street');
            $table->string('complement')->nullable()->after('number');
            $table->string('neighborhood')->nullable()->after('complement');
            $table->string('city')->nullable()->after('neighborhood');
            $table->string('state', 2)->nullable()->after('city');
            $table->string('country')->nullable()->default('Brasil')->after('state');
            // Emergency contact
            $table->string('emergency_contact_name')->nullable()->after('country');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_relationship');
            // Photo
            $table->string('photo')->nullable()->after('emergency_contact_phone');
            // Professional / access
            $table->string('access_profile')->nullable()->after('photo');
            $table->boolean('is_active')->default(true)->after('access_profile');
            $table->date('admission_date')->nullable()->after('is_active');
            $table->string('work_schedule')->nullable()->after('admission_date');
            $table->boolean('allow_system_access')->default(false)->after('work_schedule');
            $table->string('department')->nullable()->after('allow_system_access');
            $table->decimal('salary', 10, 2)->nullable()->after('role');
            $table->string('internal_code')->nullable()->after('salary');
            // Notes
            $table->text('observations')->nullable()->after('internal_code');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'social_name', 'rg', 'rg_issuer', 'rg_date', 'birth_date',
                'gender', 'marital_status', 'nationality', 'birthplace',
                'phone_secondary', 'zip_code', 'street', 'number', 'complement',
                'neighborhood', 'city', 'state', 'country',
                'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone',
                'photo', 'access_profile', 'is_active', 'admission_date',
                'work_schedule', 'allow_system_access', 'department',
                'salary', 'internal_code', 'observations',
            ]);
        });
    }
};

