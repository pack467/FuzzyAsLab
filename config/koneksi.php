<?php
$servername = "localhost";
$username = "root";
$password = "galih0249";
$dbname = "fuzzy-aslab";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>