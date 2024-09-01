<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_shop', function (Blueprint $table) {
            $table->id('sp_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shop_id')->unsigned()->index();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('barcode')->nullable();
            $table->decimal('buying_per_unit', 15, 2)->nullable()->default(0);
            $table->decimal('price_per_unit', 15, 2)->nullable()->default(0);
            $table->decimal('wholesale_price', 15,2)->nullable()->default(0);
            $table->boolean('limited')->default(true);
            $table->decimal('in_stock', 15, 2)->nullable()->default(0);
            $table->string('image')->nullable();
            $table->string('image_location')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable()->default('In Stock');
            $table->integer('reorder_point')->default(1);
            $table->string('location')->nullable();
            $table->string('product_no')->nullable();
            $table->datetime('time_created');
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
        Schema::dropIfExists('product_shop');
    }
}
