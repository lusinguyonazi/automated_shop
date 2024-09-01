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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->bigInteger('trans_id')->nullable();
            $table->string('expense_type');
            $table->decimal('amount', 15, 2)->default(0);
            $table->datetime('time_created');
            $table->datetime('expire_at')->nullable();
            $table->integer('no_days')->nullable()->default(1);
            $table->decimal('wht_rate')->nullable()->default(0);
            $table->decimal('wht_amount', 15, 2)->nullable()->default(0);
            $table->string('description')->nullable();
            $table->decimal('amount_paid', 15,2)->nullable()->default(0);
            $table->decimal('exp_vat', 12,2)->nullable()->default(0);
            $table->string('account')->default('Cash');
            $table->string('order_no')->nullable();
            $table->string('pv_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('exp_type')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->foreign('expense_category_id')->references('id')->on('expense_categories');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

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
        Schema::dropIfExists('expenses');
    }
};
