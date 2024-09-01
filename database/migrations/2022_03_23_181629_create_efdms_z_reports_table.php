<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsZReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_z_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->datetime('date');
            $table->string('tin');
            $table->string('vrn')->nullable();
            $table->string('taxoffice');
            $table->string('regid');
            $table->integer('znum');
            $table->integer('znumber');
            $table->string('efdserial');
            $table->date('registration_date');
            $table->string('user')->nullable();
            $table->string("simimsi");
            $table->string('fwversion');
            $table->string('fwchecksum');
            $table->decimal('daily_total_amount', 15, 2)->nullable()->default(0);
            $table->decimal('gross', 15, 2)->nullable()->default(0);
            $table->decimal('corrections', 15, 2)->nullable()->default(0);
            $table->decimal('discounts', 15, 2)->nullable()->default(0);
            $table->decimal('surchargs', 15, 2)->nullable()->default(0);
            $table->integer('ticketsvoid')->nullable()->default(0);
            $table->decimal('ticketsvoid_total', 15, 2)->nullable()->default(0);
            $table->integer('titcketsfiscal')->nullable()->default(0);
            $table->integer('titcketsnonfiscal')->nullable()->default(0);
            $table->string('status')->default('Not Submitted');
            $table->datetime('ack_date')->nullable();
            $table->integer('ackcode')->nullable();
            $table->string('ackmsg')->nullable();
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
        Schema::dropIfExists('efdms_z_reports');
    }
}
