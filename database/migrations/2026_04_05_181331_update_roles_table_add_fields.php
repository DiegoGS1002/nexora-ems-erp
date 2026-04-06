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
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'department')) {
                $table->string('department')->nullable()->after('name');
            }
            if (! Schema::hasColumn('roles', 'code')) {
                $table->string('code')->nullable()->after('department');
            }
            if (! Schema::hasColumn('roles', 'parent_role_id')) {
                $table->foreignId('parent_role_id')->nullable()->constrained('roles')->nullOnDelete()->after('code');
            }
            if (! Schema::hasColumn('roles', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            if (! Schema::hasColumn('roles', 'allow_assignment')) {
                $table->boolean('allow_assignment')->default(true)->after('is_active');
            }
            if (! Schema::hasColumn('roles', 'permissions')) {
                $table->json('permissions')->nullable()->after('allow_assignment');
            }
        });

        if (! collect(Schema::getIndexes('roles'))->contains('name', 'roles_code_unique')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent_role_id']);
            $table->dropColumn(['department', 'code', 'parent_role_id', 'is_active', 'allow_assignment', 'permissions']);
        });
    }
};
