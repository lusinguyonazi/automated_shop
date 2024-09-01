<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsZReportPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_z_report_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('efdms_z_report_id');
            $table->foreign('efdms_z_report_id')->references('id')->on('efdms_z_reports');
            $table->string('pmttype');
            $table->decimal('pmtamount', 15,2);
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
        Schema::dropIfExists('efdms_z_report_payments');
    }
};
