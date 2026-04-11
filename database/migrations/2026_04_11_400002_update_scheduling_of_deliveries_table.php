<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheduling_of_deliveries', function (Blueprint $table) {
            // Remove columns added before (name, description)
            $table->dropColumn(['name', 'description']);

            // Core fields
            $table->string('schedule_number')->unique()->after('id');
            $table->unsignedBigInteger('order_id')->nullable()->after('schedule_number');
            $table->string('client_name')->after('order_id');
            $table->text('delivery_address')->after('client_name');
            $table->date('delivery_date')->after('delivery_address');
            $table->unsignedBigInteger('time_window_id')->nullable()->after('delivery_date');
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('time_window_id');
            $table->unsignedBigInteger('driver_id')->nullable()->after('vehicle_id');
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->decimal('weight_kg', 10, 3)->nullable()->after('driver_name');
            $table->decimal('volume_m3', 10, 3)->nullable()->after('weight_kg');
            $table->string('priority')->default('normal')->after('volume_m3');
            $table->string('status')->default('pendente')->after('priority');
            $table->text('notes')->nullable()->after('status');
            $table->string('receiver_name')->nullable()->after('notes');
            $table->string('receiver_document')->nullable()->after('receiver_name');
            $table->timestamp('delivered_at')->nullable()->after('receiver_document');
            $table->unsignedBigInteger('rescheduled_from_id')->nullable()->after('delivered_at');
            $table->text('reschedule_reason')->nullable()->after('rescheduled_from_id');
        });
    }

    public function down(): void
    {
        Schema::table('scheduling_of_deliveries', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->dropColumn([
                'schedule_number','order_id','client_name','delivery_address',
                'delivery_date','time_window_id','vehicle_id','driver_id','driver_name',
                'weight_kg','volume_m3','priority','status','notes',
                'receiver_name','receiver_document','delivered_at',
                'rescheduled_from_id','reschedule_reason',
            ]);
        });
    }
};

