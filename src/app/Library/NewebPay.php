<?php

namespace Datomon\LaravelNewebpay\Library;

class NewebPay
{
    // ******************** ****檔案說明 *******************************
    // 藍新金流 (智付通) 金流串接的類別，有二個參數會寫在 .env 檔中，如下：
    // 藍新串接程式是使用 1.5 版本的 
    // 要串接會需要藍新商店的 HashKey、HashIV
    // ****************************************************************

    // ************************ 串接的交易參數 ***************************
    // 必要參數：
    // MerchantID => 商店代號
    // RespondType => 回傳格式 (值為 JSON 或 String)
    // TimeStamp => 時間戳記
    // Version => 串接程式版本 (值為 1.5)
    // MerchantOrderNo => 訂單編號(不可重覆)
    // Amt => 訂單總金額
    // ItemDesc => 商品資訊
    // Email => 付款人電子信箱
    // 註：「可選參數」請參考官方文件的「MPG參數設定說明」裡的交易資料參數
    // ********************************************************************


    const TEST_API = 'https://ccore.newebpay.com/MPG/mpg_gateway';  //測試站 API
    const API = 'https://core.newebpay.com/MPG/mpg_gateway';  //正式站 API


    // ************************** 生成串接的表單資料 ****************************
    // 會回傳一個陣列，裡面的資料是用來給前端的 blade 模版生成串接用的表單
    // $tradeArr => 交易資料，陣列
    // ************************************************************************
    public static function create(array $tradeArr)
    {   
        //傳來的資料陣列中，加入其他必要參數
        $tradeArr['MerchantID'] = env('NEWEBPAY_MERCHANT_ID');  //商店代號
        $tradeArr['RespondType'] = 'JSON';  //回傳格式
        $tradeArr['TimeStamp'] = strtotime('now');  //目前的時間戳記
        $tradeArr['Version'] = 1.5;  //串接程式版本

        //如果沒自訂支付完成的 url，使用此套件設定好的路由
        if(!array_key_exists('CustomerURL', $tradeArr)) {
            $tradeArr['NotifyURL'] = env('APP_URL').'/api/newebpay/receive/notifyRes';
        }

        //如果沒自訂取號完成的 url，使用此套件設定好的路由
        if(!array_key_exists('CustomerURL', $tradeArr)) {
            $tradeArr['CustomerURL'] = env('APP_URL').'/api/newebpay/receive/customerRes';
        }
        
        $aes = self::createAES($tradeArr);  //AES 加密
        $sha256 = self::createSHA256($aes);  //hash加密
        
        //回傳前端表單會用到的欄位
        return [
            'API' => (trim(env('NEWEBPAY_ENV')) === 'dev')? self::TEST_API : self::API,
            'MerchantID' => env('NEWEBPAY_MERCHANT_ID'),
            'TradeInfo' => $aes,
            'TradeSha' => $sha256,
        ];
    }


    // ***************** 將資料用 AES 加密 *******************
    // 會回傳用 AES 加密後的值
    // 必要參數：
    // $data => 要加密的資料，陣列
    // *****************************************************
    private static function createAES(array $data)
    {
        return self::create_mpg_aes_encrypt(
            $data, env('NEWEBPAY_HASH_KEY'), env('NEWEBPAY_HASH_IV')
        );
    }


    // ***************** 將資料用 AES 解密 *******************
    // 會回傳用 AES 解密後的值
    // 必要參數：
    // $aes => 用 AES 加密後的資料，字串
    // *****************************************************
    public static function decrypeAES($aes)
    {
        return self::create_aes_decrypt(
            $aes, env('NEWEBPAY_HASH_KEY'), env('NEWEBPAY_HASH_IV')
        );
    }


    // ******************* 將資料用 SHA256 加密 ***********************
    // 參考官方文件的規則做加密，會回傳用 SHA256 加密後的值
    // 必要參數：
    // $aes => 用 AES 加密後的資料，字串
    // **************************************************************
    private static function createSHA256($aes)
    {
        $str = 'HashKey='.env('NEWEBPAY_HASH_KEY').'&'.$aes.'&HashIV='.env('NEWEBPAY_HASH_IV');
        
        return strtoupper(hash('sha256', $str));  //使用 SHA256 壓碼過後，並將字串轉英文大寫
    }




    // ------------------ 以下官方提供的程式，因應函式在類別中，只改寫呼叫的方法 ---------------------

    // ****************** 官方 AES 加密程式 *********************
    private static function create_mpg_aes_encrypt ($parameter = "" , $key = "", $iv = "") 
    {
        $return_str = '';
        if (!empty($parameter)) {
            //將參數經過 URL ENCODED QUERY STRING
            $return_str = http_build_query($parameter);
        }

        return trim(bin2hex(openssl_encrypt(
            self::addpadding($return_str), 'aes-256-cbc', $key, 
            OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)
        ));
    }


    // *************** 官方 AES 加密程式會用到的小程式 ****************
    private static function addpadding($string, $blocksize = 32) 
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    // ********************** 官方 AES 解密程式 ************************
    private static function create_aes_decrypt($parameter = "", $key = "", $iv = "")
    {
        return self::strippadding(openssl_decrypt(
            hex2bin($parameter),'AES-256-CBC', $key, 
            OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv
        ));
    }

    // ***************** 官方 AES 解密程式會用到的小程式 ****************
    private static function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match('/'.$slastc.'{'.$slast.'}/', $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }
}