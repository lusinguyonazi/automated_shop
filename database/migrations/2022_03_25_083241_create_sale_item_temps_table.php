<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_temp_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_unit_id');
            $table->decimal('quantity_sold', 8, 2);
            $table->decimal('curr_stock', 8, 2);
            $table->decimal('buying_per_unit', 15, 2);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->string('used_stock')->nullable();
            $table->string('sold_in')->default('Retail Price');
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("vat_amount")->nullable()->default(0);
            $table->foreign('sale_temp_id')->references('id')->on('sale_temps');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('sale_item_temps');
    }
}
