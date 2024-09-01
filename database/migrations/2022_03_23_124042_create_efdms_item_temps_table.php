<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsItemTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('item_code');
            $table->string('desc');
            $table->decimal('qty')->nullable()->default(0);
            $table->decimal('price', 15, 2)->nullable()->default(0);
            $table->decimal('amt', 15, 2)->nullable()->default(0);
            $table->char('taxcode')->default(3);
            $table->string('with_vat')->default('no');
            $table->decimal('vat', 15, 2)->nullable()->default(0);
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
        Schema::dropIfExists('efdms_item_temps');
    }
}
