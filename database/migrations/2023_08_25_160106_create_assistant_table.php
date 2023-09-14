<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssistantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assistant', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("last_name");
            $table->string("cell_phone");
            $table->date("birthday_date");
            $table->text("municipality");
            $table->json("bank_data");
            $table->boolean("active")->default(true);
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
        Schema::dropIfExists('assistant');
    }
}
