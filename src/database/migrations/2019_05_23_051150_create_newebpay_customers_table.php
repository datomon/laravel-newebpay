<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewebpayCustomersTable extends Migration
{
    // *******************************
    // 取號完成記錄的資料表
    // *******************************

    public function up()
    {
        Schema::create('newebpay_customers', function (Blueprint $table) {
            $table->increments('id');
            // 所有支付方式都有的
            $table->string('Status', 10)->comment('取號狀態及錯誤代碼');
            $table->string('MerchantOrderNo', 30)->comment('訂單編號')->index();
            $table->integer('Amt')->nullable()->comment('交易金額');
            $table->string('TradeNo', 20)->nullable()->comment('藍新交易序號');
            $table->string('MerchantID', 15)->comment('商店代號');
            $table->dateTime('ExpireDate')->nullable()->comment('繳費截止日期');
            $table->string('PaymentType', 10)->nullable()->comment('支付方式');
            // ATM
            $table->string('BankCode', 10)->nullable()->comment('ATM-付款人銀行代碼');
            // ATM、超商代碼
            $table->string('CodeNo', 30)->nullable()->comment('ATM及超商代碼-繳費代碼');
            // 超商條碼
            $table->string('Barcode_1', 20)->nullable()->comment('超商條碼-第一段條碼資料');
            $table->string('Barcode_2', 20)->nullable()->comment('超商條碼-第二段條碼資料');
            $table->string('Barcode_3', 20)->nullable()->comment('超商條碼-第三段條碼資料');
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
        Schema::dropIfExists('newebpay_customers');
    }
}
