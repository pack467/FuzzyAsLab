<?php
require '../config/koneksi.php';

$id = $_GET['id'];

$sql = "DELETE FROM hasil_penilaian WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: daftar_hasil.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();
?>