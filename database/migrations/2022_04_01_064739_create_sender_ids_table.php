<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSenderIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sms_account_id');
            $table->string('name');
            $table->boolean('auto_sms')->default(false);
            $table->foreign('sms_account_id')->references('id')->on('sms_accounts')->onDelete('cascade');
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
        Schema::dropIfExists('sender_ids');
    }
}
