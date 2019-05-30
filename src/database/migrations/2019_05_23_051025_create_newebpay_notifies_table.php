<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewebpayNotifiesTable extends Migration
{
    // *******************************
    // 支付完成記錄的資料表
    // *******************************

    public function up()
    {
        Schema::create('newebpay_notifies', function (Blueprint $table) {
            $table->increments('id');
            // 所有支付方式都有的
            $table->string('Status', 10)->comment('交易狀態及錯誤代碼');
            $table->string('MerchantOrderNo', 30)->comment('訂單編號')->index();
            $table->integer('Amt')->nullable()->comment('交易金額');
            $table->string('TradeNo', 20)->nullable()->comment('藍新交易序號');
            $table->string('MerchantID', 15)->comment('商店代號');
            $table->dateTime('PayTime')->nullable()->comment('支付完成時間');
            $table->string('IP', 15)->nullable()->comment('付款人交易時的IP');
            $table->string('EscrowBank', 10)->nullable()->comment('款項保管銀行');
            $table->string('PaymentType', 10)->nullable()->comment('支付方式');
            // WEBATM、ATM
            $table->string('PayBankCode', 10)->nullable()->comment('WebATM、ATM支付-付款人銀行代碼');
            $table->string('PayerAccount5Code', 5)->nullable()->comment('WebATM、ATM支付-付款人銀行帳號後五碼');
            // 超商代碼繳費
            $table->string('CodeNo', 30)->nullable()->comment('超商代碼支付-繳費代碼');
            $table->integer('StoreType')->nullable()->comment('超商代碼支付-繳費門市類別');
            $table->string('StoreID')->nullable()->comment('超商代碼支付-繳費門市代號');
            // 超商條碼繳費
            $table->string('Barcode_1', 20)->nullable()->comment('超商條碼支付-第一段條碼資料');
            $table->string('Barcode_2', 20)->nullable()->comment('超商條碼支付-第二段條碼資料');
            $table->string('Barcode_3', 20)->nullable()->comment('超商條碼支付-第三段條碼資料');
            $table->string('PayStore', 8)->nullable()->comment('超商條碼支付-繳費超商');
            // 信用卡 (含 Google Pay、Samaung Pay)
            $table->string('RespondCode', 5)->nullable()->comment('信用卡支付-銀行回應碼');
            $table->string('Auth', 6)->nullable()->comment('信用卡支付-授權碼');
            $table->string('Card6No', 6)->nullable()->comment('信用卡支付-卡號前六碼');
            $table->string('Card4No', 4)->nullable()->comment('信用卡支付-卡號末四碼');
            $table->integer('Inst')->nullable()->comment('信用卡支付-分期-期別');
            $table->integer('InstFirst')->nullable()->comment('信用卡支付-分期-首期金額');
            $table->integer('InstEach')->nullable()->comment('信用卡支付-分期-每期金額');
            $table->string('ECI', 2)->nullable()->comment('信用卡支付-ECI值');
            $table->integer('TokenUseStatus')->nullable()->comment('信用卡支付-信用卡快速結帳使用狀態');
            $table->integer('RedAmt')->nullable()->comment('信用卡支付-紅利折抵後實際金額(0為折抵交易失敗)');
            $table->string('PaymentMethod', 15)->nullable()->comment('信用卡支付-信用卡類別');
            $table->float('DCC_Amt', 8, 2)->nullable()->comment('信用卡支付-外幣金額(DCC動態貨幣轉換交易才會回傳)');
            $table->float('DCC_Rate', 8, 2)->nullable()->comment('信用卡支付-匯率(DCC動態貨幣轉換交易才會回傳)');
            $table->float('DCC_Markup', 8, 2)->nullable()->comment('信用卡支付-風險匯率(DCC動態貨幣轉換交易才會回傳)');
            $table->string('DCC_Currency', 4)->nullable()->comment('信用卡支付-幣別(DCC動態貨幣轉換交易才會回傳)');
            $table->integer('DCC_Currency_Code')->nullable()->comment('信用卡支付-幣別代碼(DCC動態貨幣轉換交易才會回傳)');
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
        Schema::dropIfExists('newebpay_notifies');
    }
}
