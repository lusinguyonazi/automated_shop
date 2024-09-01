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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('position_id')->index();
            $table->string('fname');
            $table->string('lname');
            $table->string('gender');
            $table->text('address')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->boolean('is_paid_monthly')->default(true);
            $table->decimal('basic_pay_hourly', 15,2)->nullable()->default(0);
            $table->decimal('basic_pay_monthly', 15,2)->nullable()->default(0);
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('paye')->nullable();
            $table->string('sss')->nullable();
            $table->string('his')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('position_id')->references('id')->on('positions');
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
        Schema::dropIfExists('employees');
    }
};
