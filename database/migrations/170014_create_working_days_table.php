<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingDaysTable extends Migration
{
    public function up()
    {
        Schema::create('working_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('working_days');
    }
}
