### 說明 ###
台灣藍新金流(智付通)企業會員金流串接
- Laravel 版本需求：5.5 以上
- 藍新金流程式版本：1.5
- 官方API文件下載：https://www.newebpay.com/website/Page/content/download_api
- 串接藍新金流，必須登入藍新後台，創建商店，並取得該商店的 HashKey、HashIV
- 串接時建議先至藍新的測試站申請企業會員帳號、創建測試商店，做串接測試  
正式站：https://www.newebpay.com  
測試站：https://cwww.newebpay.com
- 本套件適用的支付：信用卡、Google Pay、Samsung Pay、WebATM、ATM轉帳、超商代碼繳費、條碼繳費
- 信用卡、Google Pay、Samsung Pay 須先至藍新後台的商店設定內申請啟用
- 藍新測試站所提供的測試信用卡號，可用來刷你產生的測試訂單：  
  4000-2211-1111-1111 (一次付清與分期付款)  
  4003-5511-1111-1111 (紅利折抵)  
  註：有效月年及卡片背面末三碼，任意填寫即可。另外，測試站不接受其他卡號。
- 其他本套件未說明之事項(例如：交易流程、錯誤代碼的含義)，請參考官方API文件。

### 安裝步驟 ###
(1)安裝套件
    composer require datomon/laravel-newebpay

(2)建立資料表
    php artisan migrate

(3)設定藍新金流的商店資訊參數到 .env 檔
    php artisan newebpay:init  

### 用法 ###
在你要呈現訂單頁面的控制器中，用 create 方法產生結帳按鈕會用到的資料，並指派給前端的 blade 模版即可，範例如下：  
(1)控制器
    use Datomon\LaravelNewebpay\Library\NewebPay;

    $newebpay = NewebPay::create([
        // 必填參數
        'MerchantOrderNo' => 訂單編號(不可重覆),  // 例：a00001
        'Amt' => 訂單總金額,  // 例：150
        'ItemDesc' => 商品資訊,  // 例：測試商品
        'Email' => 付款人電子信箱,  // 例：abc@example.com

        // 支付完成後，藍新傳送支付記錄的網址，此套件已寫好不必再指定。
        // 但如果你還是想自訂，就如下行去指定。但路由、控制器內容及存入資料表須行自編寫
        'NotifyURL' => 支付完成的網址,  // 例：http://www.example.com/notifyRes


        // 取號完成轉址的網址，此套件已寫好不必再指定。
        // 但如果你還是想自訂，就如下行去指定。但路由、控制器內容及存入資料表須行自編寫
        'CustomerURL' => 取號完成的網址,  // 例：http://www.example.com/customerRes 

        // 可選參數
        'ClientBackURL' => 返回商店按鈕的連結網址,  // 例：http://www.example.com/shop/123
    ]);

    return view(你訂單頁面的 blade 模版, [
        'newebpay' => $newebpay,  // newebpay 這個 key 名不要改掉
        ... 頁面其他資料的變數，以下省略 ...
    ]);

    交易資料參數必填的欄位，只剩範例中 MerchantOrderNo、Amt、ItemDesc、Email 這四項需要指定  
    可選參數，像是返回商店按鈕的連結網址、限制信用卡分期的期數等等，依你專案需要額外去指定  
    各項交易資料的參數名稱，皆與官方文件相同
(2)前端呈現訂單資料的頁面中，把按鈕元件放在你想要的位置即可
    @component('newebpay::tradeButton', [
        'newebpay' => $newebpay,
        'button' => '<button type="submit" class="button is-success">前往結帳</button>',
    ])
    @endcomponent

    按鈕的 HTML Tag，可以依你的喜好去更改，直接修改 button 的值即可
(3)取號完成轉址的 blade 模版名稱(不須副檔名 .blade.php)，請直接設定在 .env 檔的「NEWEBPAY_CUSTOMER_BLADE」參數  
從藍新傳來的資料都會在 $log 這個 Eloquent 實例中(它查詢了 newebpay_customers 資料表)，你可以在自己的 blade 模版使用此物件。例如：

    <li>取號狀態及錯誤代碼：{{ $log->Status }}</li>
    <li>訂單編號：{{ $log->MerchantOrderNo }}</li>
    <li>交易金額：{{ $log->Amt }}</li>
    <li>藍新交易序號：{{ $log->TradeNo }}</li>
    <li>商店代號：{{ $log->MerchantID }}</li>
    <li>繳費截止日期：{{ $log->ExpireDate }}</li>
    <li>支付方式：{{ $log->PaymentType }}</li>
### 其他 ###
(1)此套件有提供測試交易資料的表單，供你填部份欄位來擬模交易的流程，配合官方文件了解一些交易資料參數的用處  
請修改 .env 檔中下列的參數：
    (1)NEWEBPAY_ENV 的值設為 dev
    (2)NEWEBPAY_MERCHANT_ID、NEWEBPAY_HASH_KEY、NEWEBPAY_HASH_IV 要設為藍新測試站商店的資料
    (3)APP_URL 的值要設定專案的域名
之後瀏覽器連結：
    你的網域/newebpay/test/tradeForm
(2)取號完成後，若要看此套件預設的轉址頁面，測試交易資料的表單「CustomerURL」欄位的值請輸入：
    你的網域/api/newebpay/receive/customerRes  

    註：.env 檔「NEWEBPAY_CUSTOMER_BLADE」參數的值記得要用測試的模版名 newebpay::testCustomerRes  
(3)支付完成的記錄在 newebpay_notifies 資料表中，控制器中可以用 Eloquent 的方式讀取資料，例如：
    use Datomon\LaravelNewebpay\Models\NewebpayNotify;

    $notify = NewebpayNotify::all();
(4)取號完成的記錄在 newebpay_customers 資料表中，你控制器可以用 Eloquent 的方式讀取資料，例如：
    use Datomon\LaravelNewebpay\Models\NewebpayCustomer;

    $customer = NewebpayCustomer::all();