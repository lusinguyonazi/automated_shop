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
        Schema::create('product_made_api_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('packing_material_id')->nullable();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->decimal('cost_per_unit' ,15,2);
            $table->decimal('qty' ,  15 ,2);
            $table->decimal('unit_packed')->default(1);
            $table->decimal('selling_price' , 15 , 2)->default(0);
            $table->decimal('profit_margin' , 15 , 2)->default(0);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('packing_material_id')->references('id')->on('packing_materials');
            $table->foreign('product_id')->references('id')->on('products'); 
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
        Schema::dropIfExists('product_made_api_temps');
    }
};
