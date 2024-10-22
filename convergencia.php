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

// Calcular máximo y mínimo del período
$maximo = 0;
$minimo = PHP_INT_MAX;

foreach ($data as $kline) {
    $maximo = max($maximo, $kline[2]); // Máximo del período
    $minimo = min($minimo, $kline[3]); // Mínimo del período
}

// Calcular niveles de Fibonacci
function nivelesFibonacci($maximo, $minimo) {
    $niveles = [0.236, 0.382, 0.5, 0.618, 1];
    $fibonacci = [];

    foreach ($niveles as $nivel) {
        $fibonacci[$nivel] = $maximo - ($maximo - $minimo) * $nivel;
    }

    return $fibonacci;
}

// Mostrar los niveles de Fibonacci
$fibonacci = nivelesFibonacci($maximo, $minimo);
echo "Niveles de Fibonacci:\n";
foreach ($fibonacci as $nivel => $valor) {
    echo "$nivel: $valor\n";
}
// Calcular RSI
function calcularRSI($data, $periodo) {
    $ganancias = 0;
    $perdidas = 0;
    for ($i = 1; $i < $periodo; $i++) {
        $cambio = $data[$i][4] - $data[$i - 1][4]; // Precio cierre actual - anterior
        if ($cambio > 0) $ganancias += $cambio;
        else $perdidas += abs($cambio);
    }
    $rs = $ganancias / $perdidas;
    return 100 - (100 / (1 + $rs));
}

// Calcular RSI para los últimos 14 días
$rsi = calcularRSI($data, 14);
echo "RSI actual: $rsi\n";

// Verificar Divergencia del RSI
$precioActual = $data[14][4];
$precioAnterior = $data[0][4];

if (($precioActual > $precioAnterior && $rsi < 50) || 
    ($precioActual < $precioAnterior && $rsi > 50)) {
    echo "Posible Divergencia del RSI detectada.\n";
} else {
    echo "No se detectó divergencia significativa.\n";
}
?>
