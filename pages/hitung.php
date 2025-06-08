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
                            'output' => $output
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

// Inferensi
function evaluateRules($input, $rules) {
    $activeRules = [];
    
    foreach($rules as $rule) {
        $strength = [];
        foreach($rule['conditions'] as $var => $term) {
            $strength[] = $input[$var][$term];
        }
        $alpha = min($strength);
        
        if($alpha > 0) {
            $activeRules[] = [
                'id' => $rule['id'],
                'alpha' => $alpha,
                'output' => $rule['output'],
                'conditions' => $rule['conditions']
            ];
        }
    }
    
    return $activeRules;
}

// Defuzzifikasi
function defuzzify($activeRules) {
    $totalWeight = 0;
    $sum = 0;
    $calculations = [];

    foreach($activeRules as $rule) {
        $z = 0;
        switch($rule['output']) {
            case 'Tidak Layak': $z = 30; break;
            case 'Cukup Layak': $z = 50; break;
            case 'Sangat Layak': $z = 100; break;
        }
        
        $alpha = $rule['alpha'];
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
    'Etika' => fuzzify($_POST['etika']),
    'Tes' => fuzzify($_POST['tes']),
    'Keahlian' => fuzzify($_POST['keahlian']),
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