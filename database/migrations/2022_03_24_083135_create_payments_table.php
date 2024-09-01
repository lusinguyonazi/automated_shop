<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('transaction_id')->unique();
            $table->string('phone_number');
            $table->decimal('amount_paid', 15, 2);
            $table->string('code');
            $table->string('period')->default('Monthly');
            $table->datetime('activation_time')->nullable();
            $table->string('status')->nullable();
            $table->datetime('expire_date')->nullable();
            $table->boolean('is_expired')->default(true);
            $table->boolean('is_for_module')->default(false);
            $table->integer('module')->nullable()->default(0);
            $table->integer('subscr_type')->default(1);
            $table->boolean('is_real')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('payments');
    }
}
