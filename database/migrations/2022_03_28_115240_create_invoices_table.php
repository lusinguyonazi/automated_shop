<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('an_sale_id');
            $table->unsignedBigInteger('bank_detail_id')->nullable();
            $table->integer('inv_no');
            $table->string('order_no')->nullable();
            $table->date('due_date');
            $table->text('note')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_pass_due')->default(false);
            $table->integer('no_nots')->nullable()->default(0);
            $table->string('vehicle_no')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
            $table->foreign('bank_detail_id')->references('id')->on('bank_details');
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
        Schema::dropIfExists('invoices');
    }
}
