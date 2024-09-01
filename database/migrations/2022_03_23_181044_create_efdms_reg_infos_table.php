<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsRegInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_reg_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->string('tin', 9);
            $table->string('certkey', 14);
            $table->string('certbase');
            $table->string('file_path');
            $table->string('cert_pass');
            $table->integer('ackcode')->nullable();
            $table->string('ackmsg', 50)->nullable();
            $table->datetime('reg_date')->nullable();
            $table->string('regid', 50)->nullable();
            $table->string('serial', 14)->nullable();
            $table->string('uin')->nullable();
            $table->string('vrn')->nullable();
            $table->string('mobile')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('receiptcode')->nullable();
            $table->string('region')->nullable();
            $table->string('routing_key')->nullable();
            $table->integer('gc')->nullable();
            $table->string('taxoffice')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('tokenpath')->nullable();
            $table->char('taxcode')->nullable();
            $table->text('access_token')->nullable();
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
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
        Schema::dropIfExists('efdms_reg_infos');
    }
}
