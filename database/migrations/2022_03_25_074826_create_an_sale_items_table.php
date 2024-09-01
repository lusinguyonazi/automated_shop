<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('an_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('an_sale_id');;
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('product_unit_id');
            $table->decimal('quantity_sold', 8, 2);
            $table->decimal('buying_per_unit', 15, 2);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('total_discount', 15, 2)->nullable()->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->string('sold_in')->default('Retail Price');
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->decimal('input_tax', 15, 2)->nullable()->default(0);
            $table->integer('sync_id')->nullable();
            $table->datetime('time_created');
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('product_unit_id')->references('id')->on('product_units')->onDelete('cascade');
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
        Schema::dropIfExists('an_sale_items');
    }
}
