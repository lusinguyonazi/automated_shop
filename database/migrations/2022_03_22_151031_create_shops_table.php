<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_type_id');
            $table->unsignedBigInteger('business_type_id');
            $table->unsignedBigInteger('business_sub_type_id')->nullable();
            $table->string('suid')->unique();
            $table->string('name');
            $table->string('tel')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('street')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('logo_location')->nullable();
            $table->string('tin')->nullable();
            $table->string('vrn')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();
            $table->string('short_desc')->nullable();
            $table->string('website')->nullable();
            $table->foreign('subscription_type_id')->references('id')->on('subscription_types');
            $table->foreign('business_type_id')->references('id')->on('business_types');
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
        Schema::dropIfExists('shops');
    }
}
