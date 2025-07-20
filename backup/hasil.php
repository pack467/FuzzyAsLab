<?php

// Tambahkan di awal file untuk menangani kedua skenario (POST dan GET dari database)
require '../config/koneksi.php';

// Inisialisasi variabel
$nama = '';
$input = [];
$activeRules = [];
$output = 0;
$sum = 0;
$totalWeight = 0;
$calculations = [];
$defuzzData = [];
$rawInput = [];

// Jika diakses dari POST (hitung baru)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $input = [
        'Akademik' => fuzzify($_POST['akademik'] ?? 0),
        'Etika' => fuzzify($_POST['etika'] ?? 0),
        'Tes' => fuzzify($_POST['tes'] ?? 0),
        'Keahlian' => fuzzify($_POST['keahlian'] ?? 0),
        'Komunikasi' => fuzzify($_POST['komunikasi'] ?? 0)
    ];

    $rawInput = [
        'akademik' => (float)$_POST['akademik'],
        'etika' => (float)$_POST['etika'],
        'tes' => (float)$_POST['tes'],
        'keahlian' => (float)$_POST['keahlian'],
        'komunikasi' => (float)$_POST['komunikasi'],
    ];

    $rules = generateRules();
    $activeRules = evaluateRules($input, $rules);
    $defuzzData = defuzzify($activeRules);

    $output = $defuzzData['result'] ?? 0;
    $sum = $defuzzData['sum'] ?? 0;
    $totalWeight = $defuzzData['totalWeight'] ?? 0;
    $calculations = $defuzzData['calculations'] ?? [];
}

// Jika diakses dari GET (lihat detail)
elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM hasil_penilaian WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $nama = $row['nama'];
        $output = $row['nilai_kelulusan'];
        
        // Ambil nilai mentah
        $rawInput = [
            'akademik' => (float)$row['nilai_akademik'],
            'etika' => (float)$row['etika'],
            'tes' => (float)$row['tes_pemrograman'],
            'keahlian' => (float)$row['keahlian'],
            'komunikasi' => (float)$row['komunikasi'],
        ];

        // Fuzzifikasi dan inferensi
        $input = json_decode($row['fuzzifikasi'], true);
        $activeRules = json_decode($row['rules_digunakan'], true);
        $defuzzData = json_decode($row['defuzzifikasi'], true);

        $sum = $defuzzData['sum'] ?? 0;
        $totalWeight = $defuzzData['totalWeight'] ?? 0;
        $calculations = $defuzzData['calculations'] ?? [];
    } else {
        die("Data tidak ditemukan");
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../styles/hasil.css">
    <script>
        // Pastikan fuzzData sudah ter-inject dengan benar
        const fuzzData = <?= json_encode($input, JSON_NUMERIC_CHECK) ?>;
        console.log('FUZZ DATA:', fuzzData);
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calculator icon"></i> Hasil Perhitungan Kelayakan Calon Asisten Laboratorium</h3>
        </div>
        <div class="card-body">
            <h4><i class="fas fa-user icon"></i> Nama Calon Asisten:</h4>
            <p class="lead text-center nama-calon"><?= htmlspecialchars($nama) ?></p>

            <h4><i class="fas fa-list-ol icon"></i> Input Nilai:</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-center">
                    <tr><th>Nilai Akademik</th><td><?= htmlspecialchars($rawInput['akademik']) ?></td></tr>
                    <tr><th>Etika</th><td><?= htmlspecialchars($rawInput['etika']) ?></td></tr>
                    <tr><th>Tes Pemrograman</th><td><?= htmlspecialchars($rawInput['tes']) ?></td></tr>
                    <tr><th>Keahlian</th><td><?= htmlspecialchars($rawInput['keahlian']) ?></td></tr>
                    <tr><th>Kemampuan Komunikasi</th><td><?= htmlspecialchars($rawInput['komunikasi']) ?></td></tr>
                </table>
            </div>

            <h4 class="mt-4"><i class="fas fa-project-diagram icon"></i> Detail Fuzzifikasi:</h4>
            <?php foreach(['Akademik', 'Etika', 'Tes', 'Keahlian', 'Komunikasi'] as $var): 
                $key = strtolower($var);
                $value = $_POST[$key] ?? $rawInput[$key] ?? 0; ?>
                <div class="card card-fuzzifikasi mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?= $var ?>: <?= $value ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table calculation-table mb-0">
                            <thead>
                                <tr>
                                    <th>Himpunan</th>
                                    <th>Parameter</th>
                                    <th>Perhitungan</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $parameters = [
                                    'Rendah' => [0, 0, 20, 40],
                                    'Sedang' => [20, 40, 60, 80],
                                    'Tinggi' => [60, 80, 100, 100]
                                ];
                                
                                foreach($parameters as $set => $params): 
                                    list($a, $b, $c, $d) = $params;
                                    $hasil = $input[$var][$set] ?? 0;

                                    if ($value <= $a || $value >= $d) {
                                        $calc = "0 (Nilai di luar range)";
                                    } elseif ($value >= $b && $value <= $c) {
                                        $calc = "1 (Nilai di tengah)";
                                    } elseif ($value > $a && $value < $b) {
                                        $calc = "($value - $a) / ($b - $a) = " . round(($value - $a) / ($b - $a), 2);
                                    } else {
                                        $calc = "($d - $value) / ($d - $c) = " . round(($d - $value) / ($d - $c), 2);
                                    }
                                ?>
                                <tr>
                                    <td><?= $set ?></td>
                                    <td>(<?= implode(', ', $params) ?>)</td>
                                    <td><?= $calc ?></td>
                                    <td class="fw-bold"><?= $hasil ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Button untuk Grafik -->
                        <button id="btn-<?= $var ?>" class="btn btn-primary btn-chart">
                            <i class="fas fa-chart-line"></i> Lihat Grafik
                        </button>
                            <!-- Canvas untuk Grafik -->
                             <div id="chart-<?= $var ?>" class="chart-container" style="display: none;">
                                <canvas id="membershipChart-<?= $var ?>"></canvas>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <h4 class="mt-4"><i class="fas fa-ruler-combined icon"></i> Rules Aktif:</h4>
            <div class="table-responsive scrollable-table">
                <table class="table table-rules">
                    <thead>
                        <tr>
                            <th>Rule ID</th>
                            <th>Kondisi</th>
                            <th>Alpha</th>
                            <th>Output</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($activeRules as $rule): ?>
                        <tr>
                            <td>Rule <?= $rule['id'] ?></td>
                            <td>
                                Nilai Akademik: <?= $rule['conditions']['Akademik'] ?><br>
                                Etika: <?= $rule['conditions']['Etika'] ?><br>
                                Tes Pemrograman: <?= $rule['conditions']['Tes'] ?><br>
                                Keahlian: <?= $rule['conditions']['Keahlian'] ?><br>
                                Komunikasi: <?= $rule['conditions']['Komunikasi'] ?>
                            </td>
                            <td><?= round($rule['alpha'], 2) ?></td>
                            <td><?= $rule['output'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h4 class="mt-4"><i class="fas fa-calculator icon"></i> Proses Defuzzifikasi:</h4>
            <div class="card card-defuzzifikasi">
                <div class="card-header">
                    <h5><i class="fas fa-calculator"></i> Rumus Tsukamoto:</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Σ(α × z) / Σα</strong></p>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Perhitungan Detail:</strong>
                            <ul class="list-group mt-2 scrollable-list">
                                <?php foreach($calculations as $calc): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    [<?= $calc['output'] ?>]
                                    <span class="badge bg-primary rounded-pill">
                                        <?= round($calc['alpha'], 2) ?> × <?= $calc['z'] ?> = <?= round($calc['product'], 2) ?>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <strong>Total Keseluruhan:</strong>
                            <table class="table table-bordered mt-2">
                                <thead>
                                    <tr class="table-success">
                                        <th>Deskripsi</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <td>Σ(α × z)</td>
                                        <td><?= round($sum, 2) ?></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td>Σα</td>
                                        <td><?= round($totalWeight, 2) ?></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td>Hasil Akhir</td>
                                        <td><strong><?= round($sum, 2) ?> / <?= round($totalWeight, 2) ?> = <?= $output ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4"><i class="fas fa-chart-line icon"></i> Hasil Akhir:</h4>
            <div class="alert <?php 
                if ($output >= 70) echo "result-sangat-layak";
                elseif ($output >= 40) echo "result-cukup-layak";
                else echo "result-tidak-layak"; ?>">
                <?php if ($output < 40): ?>
                    <i class="fas fa-times-circle result-icon" style="color: #dc3545;"></i>
                <?php else: ?>
                    <i class="fas fa-check-circle result-icon" style="color: #28a745;"></i>
                <?php endif; ?>
                <div>
                    <span>Nilai Kelayakan: <strong><?= round($output, 2) ?></strong></span><br>
                    <span>Kategori: <strong>
                        <?php
                        if($output >= 70) echo "Sangat Layak";
                        elseif($output >= 40) echo "Cukup Layak";
                        else echo "Tidak Layak";
                        ?>
                    </strong></span>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-custom"><i class="fas fa-arrow-left icon"></i> Kembali</a>
                <button class="btn btn-custom"><i class="fas fa-print icon"></i> Cetak</button>
                <form method="post" action="simpan.php">
                    <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                    <input type="hidden" name="nilai_akademik" value="<?= htmlspecialchars($rawInput['akademik']) ?>">
                    <input type="hidden" name="etika" value="<?= htmlspecialchars($rawInput['etika']) ?>">
                    <input type="hidden" name="tes_pemrograman" value="<?= htmlspecialchars($rawInput['tes']) ?>">
                    <input type="hidden" name="keahlian" value="<?= htmlspecialchars($rawInput['keahlian']) ?>">
                    <input type="hidden" name="komunikasi" value="<?= htmlspecialchars($rawInput['komunikasi']) ?>">
                    <input type="hidden" name="fuzzifikasi" value="<?= htmlspecialchars(json_encode($input)) ?>">
                    <input type="hidden" name="inferensi" value="<?= htmlspecialchars(json_encode($activeRules)) ?>">
                    <input type="hidden" name="defuzzifikasi" value="<?= htmlspecialchars(json_encode($defuzzData)) ?>">
                    <input type="hidden" name="rules_digunakan" value="<?= htmlspecialchars(json_encode($activeRules)) ?>">
                    <input type="hidden" name="nilai_kelulusan" value="<?= htmlspecialchars($output) ?>">
                    <button type="submit" class="btn btn-custom"><i class="fas fa-save icon"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<footer>
    &copy; 2025 Fuzzy Tsukamoto Calon Aslab. All rights reserved.
</footer>
<script src="../scripts/chart.js"></script>
</body>
</html>