<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingMaterialShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_material_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packing_material_id');
            $table->decimal('in_store', 15, 2)->nullable()->default(0);
            $table->decimal('unit_cost', 15, 2)->nullable()->default(0);
            $table->integer('reorder_point')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('shop_id');
            $table->foreign('packing_material_id')->references('id')->on('packing_materials')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('packing_material_shop');
    }
}
