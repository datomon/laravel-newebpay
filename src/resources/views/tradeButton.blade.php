<form method="post" action="{{ $newebpay['API'] }}">
    <input type="hidden" name="MerchantID" value="{{ $newebpay['MerchantID'] }}">
    <input type="hidden" name="TradeInfo" value="{{ $newebpay['TradeInfo'] }}">
    <input type="hidden" name="TradeSha" value="{{ $newebpay['TradeSha'] }}">
    <input type="hidden" name="Version" value="1.5">
    {!! $button !!}
</form>