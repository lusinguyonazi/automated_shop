<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsRctInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_rct_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('efdms_z_report_id');
            $table->foreign('efdms_z_report_id')->references('id')->on('efdms_z_reports');
            $table->unsignedBigInteger('an_sale_id')->nullable();
            $table->datetime('date');
            $table->string('tin', 9);
            $table->string('regid', 50);
            $table->string('efdserial', 14);
            $table->string('custidtype');
            $table->string('custid')->nullable();
            $table->string('custname', 100)->nullable();
            $table->string('rctnum');
            $table->string('mobilenum')->nullable();
            $table->integer('dc');
            $table->integer('gc');
            $table->integer('znum');
            $table->string('rctvnum');
            $table->decimal('total_tax_excl', 15, 2);
            $table->decimal('total_tax_incl', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->string('status')->default('created');
            $table->boolean('is_acknowledged')->default(false);
            $table->datetime('ack_date')->nullable();
            $table->integer('ackcode')->nullable();
            $table->string('ackmsg', 50)->nullable();
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
        Schema::dropIfExists('efdms_rct_infos');
    }
}
