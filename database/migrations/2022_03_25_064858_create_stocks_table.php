<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('shop_id');;
            $table->decimal('quantity_in', 8, 2);
            $table->decimal('buying_per_unit', 15, 2);
            $table->string('source');
            $table->bigInteger('order_id')->nullable();
            $table->datetime('time_created');
            $table->date('expire_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('stocks');
    }
}
