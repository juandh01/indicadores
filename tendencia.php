<?php
// Claves API
$url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=30";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// SMA (Media Móvil Simple)
function calcularSMA($data, $periodo) {
    $suma = 0;
    for ($i = 0; $i < $periodo; $i++) {
        $suma += $data[$i][4]; 
    }
    return $suma / $periodo;
}
echo "SMA: " . calcularSMA($data, 10) . " USD\n";

// EMA (Media Móvil Exponencial)
function calcularEMA($data, $periodo) {
    $multiplicador = 2 / ($periodo + 1);
    $ema = $data[0][4];
    for ($i = 1; $i < count($data); $i++) {
        $ema = ($data[$i][4] - $ema) * $multiplicador + $ema;
    }
    return $ema;
}
echo "EMA: " . calcularEMA($data, 10) . " USD\n";

// ADX (Índice Direccional Promedio)
function calcularADX($data) {
    $adx = 0;
    for ($i = 1; $i < count($data); $i++) {
        $plusDM = max($data[$i][2] - $data[$i - 1][2], 0); 
        $minusDM = max($data[$i - 1][3] - $data[$i][3], 0); 
        $adx += abs($plusDM - $minusDM);
    }
    return $adx / (count($data) - 1);
}
echo "ADX: " . calcularADX($data) . "\n";
?>
