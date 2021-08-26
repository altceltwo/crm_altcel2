<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activation_id');
            $table->unsignedBigInteger('rate_id');
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('date_change');
            $table->timestamps();
            $table->foreign('activation_id')->references('id')->on('activations');
            $table->foreign('rate_id')->references('id')->on('rates');
            $table->foreign('offer_id')->references('id')->on('offers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historics');
    }
}
