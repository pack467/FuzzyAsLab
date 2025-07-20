<?php
require '../config/koneksi.php';

$nama = $_POST['nama'];
$nilai_akademik = (float)$_POST['nilai_akademik'];
$tes_pemrograman = (float)$_POST['tes_pemrograman'];
$keahlian = (float)$_POST['keahlian'];
$etika = (float)$_POST['etika'];
$komunikasi = (float)$_POST['komunikasi'];
$fuzzifikasi = $_POST['fuzzifikasi'];
$inferensi = $_POST['inferensi'];
$defuzzifikasi = $_POST['defuzzifikasi'];
$rules_digunakan = $_POST['rules_digunakan'];
$nilai_kelulusan = (float)$_POST['nilai_kelulusan'];

$sql = "INSERT INTO hasil_penilaian (
    nama, nilai_akademik, tes_pemrograman, keahlian, etika, komunikasi, 
    fuzzifikasi, inferensi, defuzzifikasi, rules_digunakan, nilai_kelulusan
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sdddddssssd",
    $nama,
    $nilai_akademik,
    $tes_pemrograman,
    $keahlian,
    $etika,
    $komunikasi,
    $fuzzifikasi,
    $inferensi,
    $defuzzifikasi,
    $rules_digunakan,
    $nilai_kelulusan
);

if ($stmt->execute()) {
    header("Location: daftar_hasil.php");
    exit();
} else {
    echo "Error saat menyimpan data: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>