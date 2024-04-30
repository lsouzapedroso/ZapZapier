<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wppconnect_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->unsignedBigInteger('session_id');
            $table->boolean('init');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('wppconnect_service');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wppconnect_tokens');
    }
};
