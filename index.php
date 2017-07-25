<?php
$urlBirrex = 'https://bittrex.com/api/v1.1/public/getticker?market=';
$urlCoinMarket = 'https://api.coinmarketcap.com/v1/ticker/bitcoin/';
$urlCoinMarketSummary = 'https://api.coinmarketcap.com/v1/global/';

$coinNeed = require_once ('./config.php');

$btcCap = file_get_contents($urlCoinMarket);
$btcCap = json_decode($btcCap, true, 20, JSON_BIGINT_AS_STRING);
$btcCapsummary = file_get_contents($urlCoinMarketSummary);
$btcCapsummary = json_decode($btcCapsummary, true, 20, JSON_BIGINT_AS_STRING);

$btcUsdt = file_get_contents($urlBirrex . 'USDT-BTC');
$btcUsdt = json_decode($btcUsdt, true, 20, JSON_BIGINT_AS_STRING);
$btc = "BTC: " . number_format($btcUsdt['result']['Last'], 2);

$title = $btc; //. '$ ' . number_format($btcCapsummary['total_market_cap_usd'] / 1000000, 2) . '(' . number_format($btcCap[0]['market_cap_usd'] / 1000000, 2) . ')';

$coinMarket = [];
$body = '';
foreach ($coinNeed as $coin) {
    if ($coin['status'] == 0) {
        continue;
    }

    $nameCoin = $coin['currency'] . '-' . $coin['type'];
    $value = file_get_contents($urlBirrex . $nameCoin);
    $value = json_decode($value, true, 20, JSON_BIGINT_AS_STRING);
    $exten = $coin['currency'] == 'USDT' ? '$' : 'B';
    $coinMarket[$coin['type']] = $coin['type'] . ": " . $value['result']['Last'] . " $exten " . $coin['note'];
    $body .= $coinMarket[$coin['type']] . '\n';
}

echo $title;
echo "</br>";
echo str_replace('\n', '</br>', $body);
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!Notification) {
            alert('Desktop notifications not available in your browser. Try Chromium.');
            return;
        }

        if (Notification.permission !== "granted")
            Notification.requestPermission();
    });

    function notifyMe() {
        if (Notification.permission !== "granted")
            Notification.requestPermission();
        else {
            var notification = new Notification('<?= $title; ?>', {
                icon: 'https://bittrex.com/Content/img/logos/bittrex-192.png',
                body: '<?= $body; ?>',
            });

            notification.onclick = function () {
                window.open("https://coinmarketcap.com/");
            };

        }
    }

    window.onload = function(e){
        notifyMe();
        setTimeout(function(){ location.reload(); }, 300000);
    }
</script>

