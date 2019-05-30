<?php

namespace Datomon\LaravelNewebpay\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datomon\LaravelNewebpay\Library\NewebPay;

class TestController extends Controller
{
    // ********************** 產生測試資料的交易按鈕 ***********************
    public function tradeButton(Request $request)
    {
        $data = [
            //必要參數
            'MerchantOrderNo' => $request->MerchantOrderNo,  //訂單編號(不可重覆)
            'Amt' => $request->Amt,  //訂單總金額
            'ItemDesc' => $request->ItemDesc,  //商品資訊
            'Email' => $request->Email,  //付款人電子信箱
        ];

        //可選參數
        if($request->has('ReturnURL') && $request->ReturnURL !== null) $data['ReturnURL'] = $request->ReturnURL;
        if($request->has('NotifyURL') && $request->NotifyURL !== null) $data['NotifyURL'] = $request->NotifyURL;
        if($request->has('CustomerURL') && $request->CustomerURL !== null) $data['CustomerURL'] = $request->CustomerURL;
        if($request->has('ClientBackURL') && $request->ClientBackURL !== null) $data['ClientBackURL'] = $request->ClientBackURL;
        if($request->has('InstFlag') && $request->InstFlag !== null) $data['InstFlag'] = $request->InstFlag;
        if($request->has('CREDIT') && $request->CREDIT !== null) $data['CREDIT'] = $request->CREDIT;
        if($request->has('ExpireDate') && $request->ExpireDate !== null) $data['ExpireDate'] = $request->ExpireDate;
        
        $newebpay = NewebPay::create($data);
        
        return view('newebpay::testTradeForm2', [
            'newebpay' => $newebpay,
        ]);
    }


    // ***************** 測試交易資料表單頁面 ***************
    public function tradeForm()
    {
        if(trim(env('NEWEBPAY_ENV')) !== 'dev') {
            return abort(404);
        }
        return view('newebpay::testTradeForm');
    }
}
