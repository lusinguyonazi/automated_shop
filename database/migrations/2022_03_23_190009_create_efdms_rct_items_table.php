<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEfdmsRctItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('efdms_rct_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('efdms_rct_info_id');
            $table->foreign('efdms_rct_info_id')->references('id')->on('efdms_rct_infos');
            $table->string('item_code');
            $table->string('desc')->nullable();
            $table->decimal('qty');
            $table->decimal('amt', 15, 2);
            $table->char('taxcode');
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
        Schema::dropIfExists('efdms_rct_items');
    }
}
