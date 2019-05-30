<?php

Route::group([
    'namespace' => 'Datomon\LaravelNewebpay\Http\Controllers',
    'prefix'=>'newebpay',
    'middleware' => 'web',
], function(){
    //測試交易資料
    Route::prefix('test')->group(function () {
        // 表單頁面
        Route::get('tradeForm', 'TestController@tradeForm');

        // 產生結帳按鈕
        Route::post('tradeButton', 'TestController@tradeButton');
    });
    
    // 取號完成自動導向的前端頁面
    Route::get('customerSuccess', 'BackEndController@customerSuccess')->name('newebpay.customerSuccess');
});


//後端接收藍新傳來的資料
Route::group([
    'namespace' => 'Datomon\LaravelNewebpay\Http\Controllers',
    'prefix'=> 'api/newebpay/receive',
    'middleware' => 'api',
], function(){
    // 支付完成
    Route::post('notifyRes', 'BackEndController@notifyRes');

    // 取號完成
    Route::post('customerRes', 'BackEndController@customerRes');
});