<?php
$url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=30";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// RSI (Índice de Fuerza Relativa)
function calcularRSI($data, $periodo) {
    $ganancias = 0;
    $perdidas = 0;
    for ($i = 1; $i < $periodo; $i++) {
        $cambio = $data[$i][4] - $data[$i - 1][4]; 
        if ($cambio > 0) $ganancias += $cambio;
        else $perdidas += abs($cambio);
    }
    $rs = $ganancias / $perdidas;
    return 100 - (100 / (1 + $rs));
}
echo "RSI: " . calcularRSI($data, 14) . "\n";

// Momentum
function calcularMomentum($data) {
    return $data[count($data) - 1][4] - $data[0][4];
}
echo "Momentum: " . calcularMomentum($data) . " USD\n";

// Estocástico
function calcularEstocastico($data, $periodo) {
    $minimo = $data[0][3]; // Mínimo inicial
    $maximo = $data[0][2]; // Máximo inicial
    for ($i = 1; $i < $periodo; $i++) {
        $minimo = min($minimo, $data[$i][3]); 
        $maximo = max($maximo, $data[$i][2]); 
    }
    $cierreActual = $data[$periodo - 1][4];
    return (($cierreActual - $minimo) / ($maximo - $minimo)) * 100;
}
echo "Estocástico: " . calcularEstocastico($data, 14) . "%\n";

// Williams %R
function calcularWilliamsR($data, $periodo) {
    $minimo = min(array_column(array_slice($data, 0, $periodo), 3));
    $maximo = max(array_column(array_slice($data, 0, $periodo), 2));
    $cierreActual = $data[$periodo - 1][4];
    return (($maximo - $cierreActual) / ($maximo - $minimo)) * -100;
}
echo "Williams %R: " . calcularWilliamsR($data, 14) . "%\n";
?>
