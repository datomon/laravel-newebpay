<?php

namespace Datomon\LaravelNewebpay\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datomon\LaravelNewebpay\Models\NewebpayCustomer;  //(model)取號完成
use Datomon\LaravelNewebpay\Models\NewebpayNotify;  //(model)支付完成
use Datomon\LaravelNewebpay\Library\NewebPay;  //藍新金流類別

class BackEndController extends Controller
{
    public function notifyRes(Request $request)
    {
        $json_data = NewebPay::decrypeAES($request->TradeInfo);  //AES 解碼

        if(!$json_data) throw new \Exception('AES 解密失敗!');

        // 將 json 解碼成物件
        $data = json_decode($json_data);

        if($data->Status === 'SUCCESS') {
            //所有支付都會有的資料
            $common = [
                'Status' => $data->Status,  //交易狀態
                'MerchantOrderNo' => $data->Result->MerchantOrderNo,  //訂單編號
                'Amt' => $data->Result->Amt,  //交易金額
                'TradeNo' => $data->Result->TradeNo,  //藍新交易序號
                'MerchantID' => $data->Result->MerchantID,  //藍新商店ID
                'IP' => $data->Result->IP,  //付款人交易時的IP
                'EscrowBank' => $data->Result->EscrowBank,  //款項保管銀行
                'PaymentType' => $data->Result->PaymentType,  //支付方式
            ];

            // 若不是超過取貨，加入支付完成時間 (超商取貨不會回傳 PayTime 參數)
            if($data->Result->PaymentType !== 'CVSCOM') $common['PayTime'] = $data->Result->PayTime; 
            
            //依支付方式加入不同欄位資料
            switch($data->Result->PaymentType) {
                case 'WEBATM':  //WebATM
                    $insert = array_merge($common, [
                        'PayBankCode' => $data->Result->PayBankCode,  //付款人銀行代碼
                        'PayerAccount5Code' => $data->Result->PayerAccount5Code,  //付款人銀行帳號後五碼
                    ]);
                    break;
                case 'VACC':  //ATM 轉帳
                    $insert = array_merge($common, [
                        'PayBankCode' => $data->Result->PayBankCode,  //付款人銀行代碼
                        'PayerAccount5Code' => $data->Result->PayerAccount5Code,  //付款人銀行帳號後五碼
                    ]);
                    break;
                case 'CVS':  //超商代碼
                    $insert = array_merge($common, [
                        'CodeNo' => $data->Result->CodeNo,  //繳費代碼
                        'StoreType' => $data->Result->StoreType,  //繳費門市類別
                        'StoreID' => $data->Result->StoreID,  //繳費門市代號
                    ]);
                    break;
                case 'BARCODE':  //超商條碼
                    $insert = array_merge($common, [
                        'Barcode_1' => $data->Result->Barcode_1,  //第一段條碼資料
                        'Barcode_2' => $data->Result->Barcode_2,  //第二段條碼資料
                        'Barcode_3' => $data->Result->Barcode_3,  //第三段條碼資料
                        'PayStore' => $data->Result->PayStore,  //繳費超商
                    ]);
                    break;
                case 'CREDIT':  //信用卡 (包含 Google Pay、Samaung Pay)
                    $insert = array_merge($common, [
                        'RespondCode' => $data->Result->RespondCode,  //銀行回應碼
                        'Auth' => $data->Result->Auth,  //授權碼
                        'Card6No' => $data->Result->Card6No,  //卡號前六碼
                        'Card4No' => $data->Result->Card4No,  //卡號末四碼
                        'Inst' => $data->Result->Inst,  //分期-期別
                        'InstFirst' => $data->Result->InstFirst,  //分期-首期金額
                        'InstEach' => $data->Result->InstEach,  //分期-每期金額
                        'ECI' => $data->Result->ECI,  //ECI值
                        'TokenUseStatus' => $data->Result->TokenUseStatus,  //信用卡快速結帳使用狀態
                        'PaymentMethod' => $data->Result->PaymentMethod,  //信用卡類別
                    ]);
                    
                    //若有紅利折抵 (才會傳參數)
                    if(isset($data->Result->RedAmt)) $insert['RedAmt'] = $data->Result->RedAmt;

                    //若有DCC動態貨幣轉換交易 (才會傳參數)
                    if(isset($data->Result->DCC_Amt)) $insert['DCC_Amt'] = $data->Result->DCC_Amt;
                    if(isset($data->Result->DCC_Rate)) $insert['DCC_Rate'] = $data->Result->DCC_Rate;
                    if(isset($data->Result->DCC_Markup)) $insert['DCC_Markup'] = $data->Result->DCC_Markup;
                    if(isset($data->Result->DCC_Currency)) $insert['DCC_Currency'] = $data->Result->DCC_Currency;
                    if(isset($data->Result->DCC_Currency_Code)) $insert['DCC_Currency_Code'] = $data->Result->DCC_Currency_Code;
                    break;
                default:
                    break;
            }

            NewebpayNotify::create($insert);  //新增資料
        } else {
            NewebpayNotify::create(['status' => $data->Status]);  //付款失敗將錯誤代碼存到資料表中
        }
    }

    public function customerRes(Request $request)
    {
        $json_data = NewebPay::decrypeAES($request->TradeInfo);  //AES 解碼

        if(!$json_data) throw new \Exception('AES 解密失敗!');

        // 將 json 解碼成物件
        $data = json_decode($json_data);

        if($data->Status === 'SUCCESS') {
            //所有支付都會有的資料
            $common = [
                'Status' => $data->Status,  //取號狀態
                'MerchantOrderNo' => $data->Result->MerchantOrderNo,  //訂單編號
                'Amt' => $data->Result->Amt,  //交易金額
                'TradeNo' => $data->Result->TradeNo,  //藍新交易序號
                'MerchantID' => $data->Result->MerchantID,  //藍新商店ID
                'ExpireDate' => $data->Result->ExpireDate,  //繳費截止日期
                'PaymentType' => $data->Result->PaymentType,  //支付方式
            ];

            //依支付方式組合不同欄位資料
            switch($data->Result->PaymentType) {
                case 'VACC':  //ATM 轉帳
                    $insert = array_merge($common, [
                        'BankCode' => $data->Result->BankCode,  //付款人銀行代碼
                        'CodeNo' => $data->Result->CodeNo,  //繳費代碼
                    ]);
                    break;
                case 'CVS':  //超商代碼
                    $insert = array_merge($common, [
                        'CodeNo' => $data->Result->CodeNo,  //繳費代碼
                    ]);
                    break;
                case 'BARCODE':  //超商條碼
                    $insert = array_merge($common, [
                        'Barcode_1' => $data->Result->Barcode_1,  //第一段條碼資料
                        'Barcode_2' => $data->Result->Barcode_2,  //第二段條碼資料
                        'Barcode_3' => $data->Result->Barcode_3,  //第三段條碼資料
                    ]);
                    break;
                default:
                    break;
            }

            NewebpayCustomer::create($insert);  //新增資料
        } else {
            NewebpayCustomer::create(['status' => $data->Status]);  //取號失敗將錯誤代碼存到資料表中
        }

        //重新導向到前端頁面
        return redirect()->route('newebpay.customerSuccess', [
            'orderID' => $data->Result->MerchantOrderNo  //訂單編號
        ]);
    }


    // ******************* 取號完成自動導向的前端頁面 ******************
    public function customerSuccess(Request $request)
    {
        $customer = NewebpayCustomer::where('MerchantOrderNo', $request->orderID)->first();

        return view(env('NEWEBPAY_CUSTOMER_BLADE'), ['log' => $customer]);
    }
}
