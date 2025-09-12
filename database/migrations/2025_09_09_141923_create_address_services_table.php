<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('address_services', function (Blueprint $table) {
            $table->id();
            $table->text('origin')->nullable();
            $table->text('destination')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->nullable()->references('id')->on('status')->onDelete('cascade');
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->nullable()->references('id')->on('service')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_services');
    }
};
