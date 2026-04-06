<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'tipo_pessoa')) {
                $table->string('tipo_pessoa', 2)->default('PJ')->after('id');
            }
            if (!Schema::hasColumn('clients', 'address_zip_code')) {
                $table->string('address_zip_code', 9)->nullable()->after('address');
            }
            if (!Schema::hasColumn('clients', 'address_street')) {
                $table->string('address_street')->nullable()->after('address_zip_code');
            }
            if (!Schema::hasColumn('clients', 'address_number')) {
                $table->string('address_number', 20)->nullable()->after('address_street');
            }
            if (!Schema::hasColumn('clients', 'address_complement')) {
                $table->string('address_complement', 100)->nullable()->after('address_number');
            }
            if (!Schema::hasColumn('clients', 'address_district')) {
                $table->string('address_district', 100)->nullable()->after('address_complement');
            }
            if (!Schema::hasColumn('clients', 'address_city')) {
                $table->string('address_city', 100)->nullable()->after('address_district');
            }
            if (!Schema::hasColumn('clients', 'address_state')) {
                $table->string('address_state', 2)->nullable()->after('address_city');
            }
        });
    }
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $cols = [
                'tipo_pessoa', 'address_zip_code', 'address_street',
                'address_number', 'address_complement', 'address_district',
                'address_city', 'address_state',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('clients', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
