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
        Schema::create('purchase_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('total_amount', 15, 2)->nullable()->default(0);
            $table->decimal('amount_paid', 15, 2)->nullable()->default(0);
            $table->string('currency');
            $table->string('defcurr');
            $table->string('ex_rate_mode')->default('Locale');
            $table->decimal('local_ex_rate', 15, 5)->default(1);
            $table->decimal('foreign_ex_rate', 15, 5)->default(1);
            $table->decimal('ex_rate', 15, 9)->default(1); 
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->string('order_no')->nullable();
            $table->string('delivery_note_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('date_set')->default('auto');
            $table->date('purchase_date')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('pay_type')->default('Cash');
            $table->string('status')->default('Pending');
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('purchase_temps');
    }
};
