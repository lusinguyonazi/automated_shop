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
        Schema::create('sale_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('date_set')->default('auto');
            $table->date('sale_date')->nullable();
            $table->string('sale_type')->nullable();
            $table->string('pay_type')->default('Cash');
            $table->string('currency')->nullable();
            $table->string('defcurr')->nullable();
            $table->string('ex_rate_mode')->default('Locale');
            $table->decimal('local_ex_rate', 15, 5)->default(1);
            $table->decimal('foreign_ex_rate', 15, 5)->default(1);
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->date('due_date')->nullable();
            $table->text('comments')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('sale_temps');
    }
};
