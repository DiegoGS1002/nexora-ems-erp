<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoProduto;
use App\Enums\NaturezaProduto;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->unique()->nullable()->after('id');
            $table->string('short_description', 200)->nullable()->after('description');
            $table->string('brand', 100)->nullable()->after('short_description');
            $table->string('product_type')->default(TipoProduto::Fisico->value)->after('brand');
            $table->string('nature')->default(NaturezaProduto::MercadoriaRevenda->value)->after('product_type');
            $table->string('product_line', 100)->nullable()->after('nature');
            $table->decimal('weight_net', 8, 3)->nullable()->after('product_line');
            $table->decimal('weight_gross', 8, 3)->nullable()->after('weight_net');
            $table->decimal('height', 8, 2)->nullable()->after('weight_gross');
            $table->decimal('width', 8, 2)->nullable()->after('height');
            $table->decimal('depth', 8, 2)->nullable()->after('width');
            $table->longText('full_description')->nullable()->after('depth');
            $table->boolean('is_active')->default(true)->after('full_description');
            $table->json('highlights')->nullable()->after('is_active');
            $table->json('tags')->nullable()->after('highlights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_code', 'short_description', 'brand', 'product_type',
                'nature', 'product_line', 'weight_net', 'weight_gross',
                'height', 'width', 'depth', 'full_description',
                'is_active', 'highlights', 'tags',
            ]);
        });
    }
};
