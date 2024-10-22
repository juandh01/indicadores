<?php
$url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=26";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// EMA Helper
function calcularEMA($data, $periodo) {
    $multiplicador = 2 / ($periodo + 1);
    $ema = $data[0][4];
    for ($i = 1; $i < count($data); $i++) {
        $ema = ($data[$i][4] - $ema) * $multiplicador + $ema;
    }
    return $ema;
}

// MACD (EMA 12 - EMA 26)
$ema12 = calcularEMA(array_slice($data, -12), 12);
$ema26 = calcularEMA(array_slice($data, -26), 26);
$macd = $ema12 - $ema26;
echo "MACD: $macd\n";
?>
