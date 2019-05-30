<?php

namespace Datomon\LaravelNewebpay\Console\Commands;

use Illuminate\Console\Command;

class Init extends Command
{
    //指令
    protected $signature = 'newebpay:init';

    //描述
    protected $description = '設定藍新金流的商店資訊參數到 .env 檔。用法：php artisan newebpay:init';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->comment('開始新增藍新金流商店參數，下列問題請輸入完後按 Enter，不可空白。');

        //取得輸入的參數，設定要寫入的字串
        $env = $this->ask('請問藍新金流參數的環境是? (例：測試站請輸入 dev，正式站請輸入 prod)');
        $merchant_id = $this->ask('請問藍新商店的「商店代號」是? (例：MS12345678)');
        $key = $this->ask('請問藍新商店的「HashKey」是?');
        $iv = $this->ask('請問藍新商店的「HashIV」是?');
        $customer_blade = $this->ask('請問取號完成的 blade 模版名稱是?  (例：trade.customer，測試站請輸入 newebpay::testCustomerRes)');

        $str = 'NEWEBPAY_ENV='.$env.PHP_EOL;
        $str .= 'NEWEBPAY_MERCHANT_ID='.$merchant_id.PHP_EOL;
        $str .= 'NEWEBPAY_HASH_KEY='.$key.PHP_EOL;
        $str .= 'NEWEBPAY_HASH_IV='.$iv.PHP_EOL;
        $str .= 'NEWEBPAY_CUSTOMER_BLADE='.$customer_blade.PHP_EOL;

        //取得 .env 檔位置
        $envFile = app()->environmentFilePath();

        //寫入 .env 檔末尾 ($result的值是寫入的字數)
        $result = file_put_contents($envFile, PHP_EOL.PHP_EOL.$str, FILE_APPEND);
        
        if($result) {
            $this->line('*** 藍新金流商店相關的參數已新增到 .env 檔 ***');
            return;
        } 

        $this->error('藍新金流商店相關的參數新增到 .env 檔失敗!!');
    }
}