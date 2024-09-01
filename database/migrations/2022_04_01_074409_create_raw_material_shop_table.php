<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawMaterialShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_material_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raw_material_id');
            $table->unsignedBigInteger('shop_id');
            $table->decimal('in_store', 15, 2)->nullable()->default(0);
            $table->decimal('unit_cost', 15, 2)->nullable()->default(0);
            $table->integer('reorder_point')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
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
        Schema::dropIfExists('raw_material_shop');
    }
}
