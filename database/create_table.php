<?php
require '../config/koneksi.php';

$sql = "CREATE TABLE IF NOT EXISTS hasil_penilaian (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    nilai_akademik FLOAT NOT NULL,
    etika FLOAT NOT NULL,
    tes_pemrograman FLOAT NOT NULL,
    keahlian FLOAT NOT NULL,
    komunikasi FLOAT NOT NULL,
    fuzzifikasi TEXT NOT NULL,
    inferensi TEXT NOT NULL,
    defuzzifikasi TEXT NOT NULL,
    rules_digunakan TEXT NOT NULL,
    nilai_kelulusan FLOAT NOT NULL
)";

if ($conn->query($sql)) {
    echo "Tabel berhasil dibuat";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
