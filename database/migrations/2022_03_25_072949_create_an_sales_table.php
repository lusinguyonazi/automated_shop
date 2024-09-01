<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('an_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('sale_amount', 15, 2)->nullable()->default(0);
            $table->decimal('sale_discount', 15, 2)->nullable()->default(0);
            $table->decimal('sale_amount_paid', 15, 2)->nullable()->default(0);
            $table->datetime('time_paid')->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->string('status');
            $table->string('pay_type');
            $table->string('comments')->nullable();
            $table->integer('sync_id')->nullable();
            $table->string('sale_type')->nullable();
            $table->bigInteger('sale_no')->nullable();
            $table->decimal('adjustment', 15,2)->nullable()->default(0);
            $table->bigInteger('grade_id')->nullable();
            $table->string('year')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->datetime('time_created');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('an_sales');
    }
}
