<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionCostItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_cost_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_cost_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('packing_material_id')->nullable();
            $table->decimal('unit_packed', 8, 2)->default(1);
            $table->integer('quantity');
            $table->decimal('cost_per_unit', 15, 2);
            $table->decimal('profit_margin' , 15 , 2)->nullable()->default(0);
            $table->decimal('selling_price' , 15 , 2)->nullable()->default(0);
            $table->foreign('production_cost_id')->references('id')->on('production_costs')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('packing_material_id')->references('id')->on('packing_materials')->onDelete('cascade');     
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
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
        Schema::dropIfExists('production_cost_items');
    }
}
