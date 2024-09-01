<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceServitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_servitems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pro_invoice_id');
            $table->unsignedBigInteger('service_id');
            // $table->text('description');
            $table->integer('repeatition');
            $table->decimal('cost_per_unit', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->decimal("tax_amount")->nullable()->default(0);
            $table->datetime('time_created');
            $table->foreign('pro_invoice_id')->references('id')->on('pro_invoices')->onDelete('cascade');
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
        Schema::dropIfExists('invoice_servitems');
    }
}
