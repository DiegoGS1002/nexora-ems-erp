<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTable extends Migration
{
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identification_number')->unique();
            $table->string('role');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('address');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee');
    }
}
