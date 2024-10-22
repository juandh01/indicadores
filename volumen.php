<?php
$url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=30";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// On-Balance Volume (OBV)
function calcularOBV($data) {
    $obv = 0;
    for ($i = 1; $i < count($data); $i++) {
        if ($data[$i][4] > $data[$i - 1][4]) $obv += $data[$i][5]; 
        elseif ($data[$i][4] < $data[$i - 1][4]) $obv -= $data[$i][5];
    }
    return $obv;
}
echo "OBV: " . calcularOBV($data) . "\n";

// Acumulación/Distribución (A/D)
function calcularAD($data) {
    $ad = 0;
    for ($i = 0; $i < count($data); $i++) {
        $clv = (($data[$i][4] - $data[$i][3]) - ($data[$i][2] - $data[$i][4])) / ($data[$i][2] - $data[$i][3]);
        $ad += $clv * $data[$i][5];
    }
    return $ad;
}
echo "A/D: " . calcularAD($data) . "\n";

// Cálculo básico del Parabolic SAR
function calcularParabolicSAR($data, $af = 0.02, $afMax = 0.2) {
    $sar = $data[0][3]; // Mínimo inicial
    $ep = $data[0][2]; // Máximo inicial
    $uptrend = true; // Definir si es tendencia alcista

    for ($i = 1; $i < count($data); $i++) {
        $sar += $af * ($ep - $sar);
        if ($uptrend) {
            if ($data[$i][3] < $sar) {
                $uptrend = false;
                $sar = $ep;
                $ep = $data[$i][3];
                $af = 0.02;
            }
        } else {
            if ($data[$i][2] > $sar) {
                $uptrend = true;
                $sar = $ep;
                $ep = $data[$i][2];
                $af = 0.02;
            }
        }
        $ep = $uptrend ? max($ep, $data[$i][2]) : min($ep, $data[$i][3]);
        $af = min($afMax, $af + 0.02);
    }
    return $sar;
}
echo "Parabolic SAR: " . calcularParabolicSAR($data) . "\n";

// Cálculo del CMF
function calcularCMF($data) {
    $cmf = 0;
    for ($i = 0; $i < count($data); $i++) {
        $clv = (($data[$i][4] - $data[$i][3]) - ($data[$i][2] - $data[$i][4])) / ($data[$i][2] - $data[$i][3]);
        $cmf += $clv * $data[$i][5];
    }
    return $cmf;
}
echo "CMF: " . calcularCMF($data) . "\n";
?>
