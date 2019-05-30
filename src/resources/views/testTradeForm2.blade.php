<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css" />
    <title>藍新金流交易測試</title>
</head>
<body>
    <section class="section">
        <div class="container">
            <h2 class="title">藍新金流交易測試 (步驟二)</h2>
            <div class="notification is-warning">
                請點擊「前往結帳」按鈕，將交易資料傳送給藍新金流。
            </div>

            {{--  結帳按鈕  --}}
            @component('newebpay::tradeButton', [
                'newebpay' => $newebpay,
                'button' => '<button type="submit" class="button is-success">前往結帳</button>',
            ])
            @endcomponent
        </div>
    </section>
</body>
</html>

