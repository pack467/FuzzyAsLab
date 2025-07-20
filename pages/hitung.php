<?php
// Fungsi Keanggotaan Trapesium
function trapezoid($x, $params) {
    list($a, $b, $c, $d) = $params;
    
    if ($x <= $a || $x >= $d) {
        return 0;
    } elseif ($x >= $b && $x <= $c) {
        return 1;
    } elseif ($x > $a && $x < $b) {
        return ($x - $a) / ($b - $a);
    } else {
        return ($d - $x) / ($d - $c);
    }
}

// Fungsi Keanggotaan Monoton untuk Output (Tsukamoto)
function monotonOutput($alpha, $type, $params) {
    if ($alpha <= 0) return $params[0];
    if ($alpha >= 1) return $params[1];
    
    switch($type) {
        case 'naik': 
            return $params[0] + $alpha * ($params[1] - $params[0]);
        case 'turun': 
            return $params[1] - $alpha * ($params[1] - $params[0]);
        default:
            return $params[0];
    }
}

// Fuzzifikasi
function fuzzify($value) {
    $value = (float)$value;
    $rendah = trapezoid($value, [0, 0, 20, 40]);
    $sedang = trapezoid($value, [20, 40, 60, 80]);
    $tinggi = trapezoid($value, [60, 80, 100, 101]);
    
    return [
        'Rendah' => round($rendah, 2),
        'Sedang' => round($sedang, 2),
        'Tinggi' => round($tinggi, 2)
    ];
}

// Generate Rules
function generateRules() {
    $kategori = ['Rendah', 'Sedang', 'Tinggi'];
    $rules = [];
    $ruleId = 1;
    
    // Parameter untuk fungsi output monoton Tsukamoto
$outputParams = [
    'Tidak Layak' => ['type' => 'turun', 'params' => [40, 0]],
    'Cukup Layak' => ['type' => 'naik', 'params' => [30, 70]],  
    'Sangat Layak' => ['type' => 'naik', 'params' => [60, 100]] 
];
    
    foreach($kategori as $akademik) {
        foreach($kategori as $tes) {
            foreach($kategori as $keahlian) {
                foreach($kategori as $etika) {
                    foreach($kategori as $komunikasi) {
                        $output = tentukanOutput($akademik, $tes, $keahlian, $etika, $komunikasi);
                        
                        $rules[] = [
                            'id' => $ruleId++,
                            'conditions' => [
                                'Akademik' => $akademik,
                                'Tes' => $tes,
                                'Keahlian' => $keahlian,
                                'Etika' => $etika,
                                'Komunikasi' => $komunikasi
                            ],
                            'output' => $output,
                            'outputType' => $outputParams[$output]['type'],
                            'outputParams' => $outputParams[$output]['params']
                        ];
                    }
                }
            }
        }
    }
    
    return $rules;
}

function tentukanOutput($akademik, $tes, $keahlian, $etika, $komunikasi) {
    if(in_array('Rendah', [$akademik, $tes, $keahlian])) {
        return 'Tidak Layak';
    }
    
    $countTinggi = array_count_values([$akademik, $tes, $keahlian, $etika, $komunikasi])['Tinggi'] ?? 0;
    if($countTinggi >= 3) {
        return 'Sangat Layak';
    }
    
    return 'Cukup Layak';
}

// Inferensi Tsukamoto
function evaluateRules($input, $rules) {
    $activeRules = [];
    
    foreach($rules as $rule) {
        $strength = [];
        foreach($rule['conditions'] as $var => $term) {
            $strength[] = $input[$var][$term];
        }
        $alpha = min($strength);
        
        if($alpha > 0) {
            // Hitung nilai crisp output menggunakan fungsi monoton
            $crispOutput = monotonOutput($alpha, $rule['outputType'], $rule['outputParams']);
            
            $activeRules[] = [
                'id' => $rule['id'],
                'alpha' => $alpha,
                'z' => round($crispOutput, 2), // z dinamis berdasarkan alpha
                'output' => $rule['output'],
                'conditions' => $rule['conditions']
            ];
        }
    }
    
    return $activeRules;
}

// Defuzzifikasi Weighted Average
function defuzzify($activeRules) {
    $totalWeight = 0;
    $sum = 0;
    $calculations = [];

    foreach($activeRules as $rule) {
        $alpha = $rule['alpha'];
        $z = $rule['z']; // z sudah dihitung dari fungsi monoton
        $product = $alpha * $z;
        
        $sum += $product;
        $totalWeight += $alpha;
        
        $calculations[] = [
            'alpha' => $alpha,
            'z' => $z,
            'product' => $product,
            'output' => $rule['output']
        ];
    }

    return [
        'result' => $totalWeight > 0 ? round($sum / $totalWeight, 2) : 0,
        'sum' => $sum,
        'totalWeight' => $totalWeight,
        'calculations' => $calculations
    ];
}

// Proses Input
$nama = $_POST['nama'];
$input = [
    'Akademik' => fuzzify($_POST['akademik']),
    'Tes' => fuzzify($_POST['tes']),
    'Keahlian' => fuzzify($_POST['keahlian']),
    'Etika' => fuzzify($_POST['etika']),
    'Komunikasi' => fuzzify($_POST['komunikasi'])
];

$rules = generateRules();
$activeRules = evaluateRules($input, $rules);

$defuzzData = defuzzify($activeRules);
$output = $defuzzData['result'];
$sum = $defuzzData['sum'];
$totalWeight = $defuzzData['totalWeight'];
$calculations = $defuzzData['calculations'];

include 'hasil.php';
?>