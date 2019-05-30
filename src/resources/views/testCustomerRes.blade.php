<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>藍新金流交易測試-取號完成</title>
</head>
<body>
    <h1>藍新金流交易測試-取號完成</h1>
    
    <li>取號狀態及錯誤代碼：{{ $log->Status }}</li>
    <li>訂單編號：{{ $log->MerchantOrderNo }}</li>
    <li>交易金額：{{ $log->Amt }}</li>
    <li>藍新交易序號：{{ $log->TradeNo }}</li>
    <li>商店代號：{{ $log->MerchantID }}</li>
    <li>繳費截止日期：{{ $log->ExpireDate }}</li>
    <li>支付方式：{{ $log->PaymentType }}</li>
</body>
</html>