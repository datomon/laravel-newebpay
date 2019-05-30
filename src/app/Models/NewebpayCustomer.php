<?php

namespace Datomon\LaravelNewebpay\Models;

use Illuminate\Database\Eloquent\Model;

class NewebpayCustomer extends Model
{
    //批量賦值
    protected $fillable = ['Status', 'MerchantOrderNo', 'Amt', 'TradeNo', 'MerchantID', 'ExpireDate', 'PaymentType', 'BankCode', 'CodeNo', 'Barcode_1', 'Barcode_2', 'Barcode_3'];

    protected $hidden = ['updated_at'];
}
