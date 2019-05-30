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
            <h2 class="title">藍新金流交易測試 (步驟一)</h2>
            <div class="notification is-warning">
                <p>本頁讓你快速帶入幾個交易資料欄位，模擬送出交易資料後，轉頁到藍新測試站的情況。</p>
                <br>
                <p>要使用本頁的功能，請先至藍新測試站申請企業會員帳號，並創建測試商店，將商店代號、HashKey、HashIV 設定在專案的 .env 檔中。</p>
                <br>
                <p>必填、可選欄位填完後，請點擊最下方的「製作結帳按鈕」。</p>
                <br>
                <p>信用卡支付(一次付清、分期)，以及 Google Pay、Samaung Pay 都必須先到藍新的商店設定中申請啟用。</p>
                <br>
                <p>
                    本頁會用到 .env 檔的下面三個參數，請將值設為藍新「測試站」商店的資料。<span class="has-background-danger has-text-white-bis">若要關閉此頁面的功能</span>，把參數「NEWEBPAY_ENV」的值改為 prod 即可。
                    <li>
                        NEWEBPAY_MERCHANT_ID  
                        <span class="tag is-dark">商店代號</span>
                    </li>
                    <li>
                        NEWEBPAY_HASH_KEY  
                        <span class="tag is-dark">商店 HashKey</span>
                    </li>
                    <li>
                        NEWEBPAY_HASH_IV  
                        <span class="tag is-dark">商店 HashIV</span>
                    </li>
                </p>
            </div>

            {{--  form  --}}
            <form method="post" action="{{ url('newebpay/test/tradeButton') }}">
                <h3 class="title is-3">必填欄位</h3>
                {{ csrf_field() }}
                <div class="field">
                    <label class="label">MerchantOrderNo - 訂單編號(不可重覆)</label>
                    <div class="control">
                        <input class="input is-danger" type="text" placeholder="例如，A0001" name="MerchantOrderNo">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Amt - 訂單總金額</label>
                    <div class="control">
                        <input class="input is-danger" type="text" placeholder="例如，100" name="Amt">
                    </div>
                </div>
                <div class="field">
                    <label class="label">ItemDesc - 商品資訊</label>
                    <div class="control">
                        <input class="input is-danger" type="text" placeholder="例如，測試商品" name="ItemDesc">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Email - 付款人電子信箱</label>
                    <div class="control">
                        <input class="input is-danger" type="text" placeholder="例如，a123@example.com" name="Email">
                    </div>
                </div>
                <hr>
                <h3 class="title is-3">可選欄位</h3>
                    <div class="field">
                        <label class="label">ReturnURL - 即時交易支付完成後，client 端會以 Form Post 方式自動導回的網址 (建議直接於藍新後台商店中，設定在 Return URL 欄位內)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，https://abc.excample.com/paysuccess" name="ReturnURL">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">NotifyURL - 支付完成後，藍新伺服器把支付記錄以 Form Post 方式傳送到此網址 (此套件已寫好，如果你要改自訂的網址再填寫此參數)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，https://abc.excample.com/paidlog" name="NotifyURL">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">CustomerURL - 取號完成後(非即時交易)，藍新金流會把取號記錄以 Form Post 方式傳送到此網址，client 端也會自動導向到此網址 (此套件已寫好，如果你要改自訂的網址再填寫此參數)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，https://abc.excample.com/customerlog" name="CustomerURL">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">ClientBackURL - 藍新支付頁面「返回商店」按鈕的連結網址</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，https://abc.excample.com/product/234" name="ClientBackURL">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">InstFlag - 限制信用卡分期的期數 (必須先至藍新的商店申請啟用此功能並查看能分的期數有哪些，預設是全開。若你想限制只能分 3 期就輸入 3。另外，若用 3,6 則二個期數都開啟)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，3" name="InstFlag">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">CREDIT - 開啟信用卡一次付清 (若你有限制信用卡分期，又想開放一次付清的功能，此項必需要帶入 1。另外，若不限制分期則一次付清功能會直接開啟，就不必帶入此欄位)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，1" name="CREDIT">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">ExpireDate - 繳費有效期限 (適用於非即時交易，若未設定則預設 7 天，格式為 Ymd)</label>
                        <div class="control">
                            <input class="input is-success" type="text" placeholder="例如，20140620" name="ExpireDate">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="button is-info is-medium is-fullwidth">製作結帳按鈕</button>
            </form>
        </div>
    </section>
</body>
</html>