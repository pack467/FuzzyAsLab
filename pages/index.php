<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fuzzy Tsukamoto – Kelayakan Asisten Lab</title>

  <!-- Google Fonts & FontAwesome -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/index.css"/>
</head>
<body>
        <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header">
                <div class="logo-container">
                    <div class="logo">
                        <img src="../images/logo.png" alt="Logo">
                    </div>
                </div>
                <h2 class="fw-bold text-white mb-0">Penilaian Kelayakan Asisten Lab</h2>
                
                <!-- Navigation Button -->
                <div class="mt-3">
                    <a href="daftar_hasil.php" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-list me-2"></i>Lihat Daftar Hasil
                    </a>
                </div>
            </div>

      <div class="card-body p-4">
        <form action="hitung.php" method="post" class="needs-validation" novalidate>
          <!-- Nama -->
          <div class="mb-3 position-relative">
            <label class="form-label">Nama Calon Asisten</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
              <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap"/>
            </div>
            <div class="invalid-feedback">Nama tidak boleh kosong.</div>
          </div>
          
          <!-- Nilai Akademik -->
          <div class="mb-3 position-relative">
            <label class="form-label">Nilai Akademik</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
              <input type="number" name="akademik" class="form-control" required
                     min="0" max="100" step="0.01" placeholder="0 - 100"/>
            </div>
            <div class="invalid-feedback">Masukkan nilai antara 0–100.</div>
          </div>
          
          <!-- Tes Pemrograman -->
          <div class="mb-3 position-relative">
            <label class="form-label">Tes Pemrograman</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-code"></i></span>
              <input type="number" name="tes" class="form-control" required
                     min="0" max="100" step="0.01" placeholder="0 - 100"/>
            </div>
            <div class="invalid-feedback">Masukkan nilai antara 0–100.</div>
          </div>
          
          <!-- Keahlian -->
          <div class="mb-3 position-relative">
            <label class="form-label">Keahlian</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-toolbox"></i></span>
              <input type="number" name="keahlian" class="form-control" required
                     min="0" max="100" step="0.01" placeholder="0 - 100"/>
            </div>
            <div class="invalid-feedback">Masukkan nilai antara 0–100.</div>
          </div>
          
          <!-- Etika -->
          <div class="mb-3 position-relative">
            <label class="form-label">Etika</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-handshake"></i></span>
              <input type="number" name="etika" class="form-control" required
                     min="0" max="100" step="0.01" placeholder="0 - 100"/>
            </div>
            <div class="invalid-feedback">Masukkan nilai antara 0–100.</div>
          </div>
          
          <!-- Komunikasi -->
          <div class="mb-4 position-relative">
            <label class="form-label">Kemampuan Komunikasi</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-comments"></i></span>
              <input type="number" name="komunikasi" class="form-control" required
                     min="0" max="100" step="0.01" placeholder="0 - 100"/>
            </div>
            <div class="invalid-feedback">Masukkan nilai antara 0–100.</div>
          </div>
          
          <button type="submit" class="btn btn-lg btn-primary w-100 shadow-sm">
            <i class="fa-solid fa-calculator me-2"></i>Hitung Kelayakan
          </button>
        </form>
      </div>
      
      <div class="card-footer text-center bg-light border-0">
        <small class="text-muted">&copy; 2025 Fuzzy Tsukamoto Calon Aslab UINSU. All rights reserved.</small>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS & Form Validation -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../scripts/index.js"></script>
</body>
</html>