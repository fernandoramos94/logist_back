<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('client');

            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('unit');

            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id')->references('id')->on('driver');

            $table->unsignedBigInteger('assistant_id');
            $table->foreign('assistant_id')->references('id')->on('assistant');

            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('status');
            

            $table->text("destination_address");
            $table->text("origin_address");

            $table->date("upload_date");
            $table->string("charging_hour");
            $table->date("download_date");
            $table->string("download_time");

            $table->integer("bands")->nullable();
            $table->integer("roller_skates")->nullable();
            $table->integer("beach")->nullable();
            $table->integer("devils")->nullable();
            $table->integer("mats")->nullable();
            $table->integer("cartons")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service');
    }
}
