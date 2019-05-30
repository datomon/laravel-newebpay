<?php

namespace Datomon\LaravelNewebpay\Models;

use Illuminate\Database\Eloquent\Model;

class NewebpayNotify extends Model
{
    //批量賦值
    protected $fillable = ['Status', 'MerchantOrderNo', 'Amt', 'TradeNo', 'MerchantID', 'PayTime', 'IP', 'EscrowBank', 'PaymentType', 'PayBankCode', 'PayerAccount5Code', 'CodeNo', 'StoreType', 'StoreID', 'Barcode_1', 'Barcode_2', 'Barcode_3', 'PayStore', 'RespondCode', 'Auth', 'Card6No', 'Card4No', 'Inst', 'InstFirst', 'InstEach', 'ECI', 'TokenUseStatus', 'RedAmt', 'PaymentMethod', 'DCC_Amt', 'DCC_Rate', 'DCC_Markup', 'DCC_Currency', 'DCC_Currency_Code'];

    protected $hidden = ['updated_at'];
}
