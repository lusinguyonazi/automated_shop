<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('an_sale_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('no_of_repeatition')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->datetime('time_created');
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
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
        Schema::dropIfExists('service_sale_items');
    }
}
