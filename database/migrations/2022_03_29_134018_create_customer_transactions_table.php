<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->integer('invoice_no')->nullable();
            $table->boolean('is_ob')->default(false);
            $table->unsignedBigInteger('cash_out_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->integer('receipt_no')->nullable();
            $table->decimal('payment', 15, 2)->nullable();
            $table->decimal('trans_invoice_amount', 15, 2)->nullable()->default(0);
            $table->decimal('trans_ob_amount', 15,2)->nullable()->default(0);
            $table->decimal('trans_credit_amount', 15,2)->nullable()->default(0);
            $table->boolean('is_utilized')->default(true);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 6)->default(1); 
            $table->string('payment_mode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('cn_no')->nullable();
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->decimal('ob_paid', 15,2)->nullable()->default(0);
            $table->date('date');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('customer_transactions');
    }
}