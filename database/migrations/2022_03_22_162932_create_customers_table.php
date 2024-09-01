<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tin')->nullable();
            $table->boolean('check_last_sale')->default(false);
            $table->integer('cust_no');
            $table->string('vrn')->nullable();
            $table->string('country_code')->nullable()->default('TZ');
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();
            $table->string('street')->nullable();
            $table->integer('cust_id_type')->default(6); //For VFD
            $table->string('custid')->nullable(); //For VFD
            $table->datetime('time_created');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
}
