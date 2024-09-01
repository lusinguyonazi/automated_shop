<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->string('grn_no')->nullable();
            $table->string('order_no')->nullable();
            $table->string('delivery_note_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable()->default(0);
            $table->decimal('amount_paid', 15, 2)->nullable()->default(0);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->string('purchase_type')->nullable()->default('cash');
            $table->string('status')->default('Pending');
            $table->text('comments')->nullable();
            $table->datetime('time_created');
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
