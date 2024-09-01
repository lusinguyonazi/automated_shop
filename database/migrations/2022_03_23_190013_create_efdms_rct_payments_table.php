<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsRctPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_rct_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('efdms_rct_info_id');
            $table->foreign('efdms_rct_info_id')->references('id')->on('efdms_rct_infos');
            $table->string('pmttype');
            $table->decimal('pmtamount', 15, 2);
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
        Schema::dropIfExists('efdms_rct_payments');
    }
};
