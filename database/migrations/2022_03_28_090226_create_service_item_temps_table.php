<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceItemTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_temp_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('no_of_repeatition')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->difault(0);
            $table->decimal('total_discount', 15,2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("vat_amount")->nullable()->default(0);
            $table->foreign('sale_temp_id')->references('id')->on('sale_temps');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
        Schema::dropIfExists('service_item_temps');
    }
}
