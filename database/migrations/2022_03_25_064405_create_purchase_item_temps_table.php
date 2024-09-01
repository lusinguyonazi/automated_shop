<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_temp_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_in')->default(0);
            $table->decimal('buying_per_unit', 15, 2)->default(0);
            $table->decimal('price_per_unit', 15,2)->default(0);
            $table->date('expire_date')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->foreign('purchase_temp_id')->references('id')->on('purchase_temps')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_item_temps');
    }
}
