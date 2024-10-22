<?php
$url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=20";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Bandas de Bollinger
function calcularBandasBollinger($data, $periodo) {
    $suma = 0;
    for ($i = 0; $i < $periodo; $i++) {
        $suma += $data[$i][4];
    }
    $media = $suma / $periodo;
    $sumaDesviacion = 0;
    for ($i = 0; $i < $periodo; $i++) {
        $sumaDesviacion += pow($data[$i][4] - $media, 2);
    }
    $desviacion = sqrt($sumaDesviacion / $periodo);
    $bandaSuperior = $media + (2 * $desviacion);
    $bandaInferior = $media - (2 * $desviacion);
    return [$bandaSuperior, $media, $bandaInferior];
}
list($bandaSup, $media, $bandaInf) = calcularBandasBollinger($data, 20);
echo "Bandas de Bollinger:\nSuperior: $bandaSup\nMedia: $media\nInferior: $bandaInf\n";

// Average True Range (ATR)
function calcularATR($data, $periodo) {
    $atr = 0;
    for ($i = 1; $i < $periodo; $i++) {
        $rango = max($data[$i][2] - $data[$i][3], abs($data[$i][2] - $data[$i - 1][4]), abs($data[$i][3] - $data[$i - 1][4]));
        $atr += $rango;
    }
    return $atr / $periodo;
}
echo "ATR: " . calcularATR($data, 14) . "\n";
?>
