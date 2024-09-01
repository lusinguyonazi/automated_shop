<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->boolean('allow_multi_currency')->default(false);
            $table->decimal('tax_rate')->default(18);
            $table->string('business_account')->nullable();
            $table->string('inv_no_type')->nullable();
            $table->boolean('is_vat_registered')->default(false);
            $table->boolean('estimate_withholding_tax')->default(false);
            $table->boolean('use_barcode')->default(false);
            $table->boolean('always_sell_old')->default(true);
            $table->boolean('allow_sp_less_bp')->default(false);
            $table->boolean('is_service_per_device')->default(false);
            $table->string('currency_words')->default('Tanzania')->nullable();
            $table->boolean('retail_with_wholesale')->default(false);
            $table->boolean('allow_unit_discount')->default(false);
            $table->boolean('is_school')->default(false);
            $table->boolean('enable_exp_date')->default(false);
            $table->boolean('show_bd')->default(false);
            $table->boolean('is_agent')->default(false);
            $table->boolean('enable_cpos')->default(false);
            $table->integer('sp_mindays')->default(3);
            $table->boolean('is_categorized')->default(false);
            $table->boolean('show_discounts')->default(true);
            $table->boolean('show_end_note')->default(true);
            $table->string('invoice_title_position')->nullable()->default('right');
            $table->boolean('enable_efd')->default(false);
            $table->boolean('generate_barcode')->default(false);
            $table->boolean('use_vfd_only')->default(false);
            $table->boolean('always_issue_vfd')->default(false);
            $table->boolean('disable_prod_panel')->default(false);
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
        Schema::dropIfExists('settings');
    }
}
