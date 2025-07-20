<?php
require '../config/koneksi.php';

// Ambil semua data hasil_penilaian, urutkan dari nilai akhir tertinggi ke terendah
$sql    = "SELECT * FROM hasil_penilaian ORDER BY nilai_kelulusan DESC";
$result = $conn->query($sql);
$total  = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hasil Penilaian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../styles/daftar_hasil.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="fas fa-list-alt me-2"></i> Daftar Hasil Penilaian Calon Asisten Lab</h2>
                    <a href="index.php" class="btn btn-light"><i class="fas fa-plus me-2"></i>Penilaian Baru</a>
                </div>
            </div>
            
            <div class="card-body">
                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">Ringkasan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Detail Nilai</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="myTabContent">
                    <!-- Ringkasan Nilai Akhir -->
                    <div class="tab-pane fade show active" id="summary" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama Calon</th>
                                        <th width="15%">Nilai Akhir</th>
                                        <th width="20%">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Kategori</span>
                                                <div class="progress" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                                </div>
                                            </div>
                                        </th>
                                        <th width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($row = $result->fetch_assoc()): 
                                        // Tentukan kategori berdasarkan nilai_kelulusan
                                        if($row['nilai_kelulusan'] >= 70) {
                                            $kategori = 'Sangat Layak';
                                            $badge     = 'success';
                                        } elseif($row['nilai_kelulusan'] >= 40) {
                                            $kategori = 'Cukup Layak';
                                            $badge     = 'warning';
                                        } else {
                                            $kategori = 'Tidak Layak';
                                            $badge     = 'danger';
                                        }
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($row['nama']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="fw-bold me-2"><?= number_format($row['nilai_kelulusan'], 2) ?></div>
                                                <?php if($row['nilai_kelulusan'] >= 70): ?>
                                                    <i class="fas fa-arrow-up text-success"></i>
                                                <?php elseif($row['nilai_kelulusan'] >= 40): ?>
                                                    <i class="fas fa-minus text-warning"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-arrow-down text-danger"></i>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $badge ?> rounded-pill">
                                                <i class="fas <?= $badge == 'success' ? 'fa-check' : ($badge == 'warning' ? 'fa-exclamation' : 'fa-times') ?> me-1"></i>
                                                <?= $kategori ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="hasil.php?id=<?= $row['id'] ?>" class="btn btn-detail btn-sm me-2 text-white">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-hapus btn-sm text-white" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Detail Setiap Kriteria -->
                    <div class="tab-pane fade" id="details" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Calon</th>
                                        <th width="15%">Akademik</th>
                                        <th width="15%">Tes Pemrograman</th>
                                        <th width="15%">Keahlian</th>
                                        <th width="15%">Etika</th>
                                        <th width="15%">Komunikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Reset pointer agar loop kedua tetap sesuai urutan sorting
                                    $result->data_seek(0);
                                    $no = 1;
                                    while($row = $result->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $row['nilai_akademik'] ?>%;">
                                                    <?= $row['nilai_akademik'] ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $row['tes_pemrograman'] ?>%;">
                                                    <?= $row['tes_pemrograman'] ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $row['keahlian'] ?>%;">
                                                    <?= $row['keahlian'] ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $row['etika'] ?>%;">
                                                    <?= $row['etika'] ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $row['komunikasi'] ?>%;">
                                                    <?= $row['komunikasi'] ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-2"></i> Total <?= $total ?> Data Ditemukan
                    </div>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <a href="index.php" class="btn btn-primary rounded-circle floating-btn">
        <i class="fas fa-plus"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
    </script>
</body>
</html>