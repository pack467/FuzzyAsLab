// script.js - perbaikan untuk menampilkan fungsi keanggotaan trapezoid penuh

// Fungsi untuk menampilkan/sembunyikan grafik
function toggleChart(variable, value) {
  const chartContainer = document.getElementById(`chart-${variable}`);
  const chartCanvas = document.getElementById(`membershipChart-${variable}`);
  const button = document.getElementById(`btn-${variable}`);

  // Cek apakah chart sudah ada
  const chartExists = window[`chart_${variable}`] instanceof Chart;

  if (chartContainer.style.display === "none" || !chartExists) {
    chartContainer.style.display = "block";
    button.innerHTML = '<i class="fas fa-times"></i> Tutup Grafik';
    button.classList.replace('btn-primary', 'btn-danger');

    // Hancurkan chart lama jika ada
    if (chartExists) {
      window[`chart_${variable}`].destroy();
    }

    renderChart(variable, parseFloat(value), chartCanvas);
  } else {
    chartContainer.style.display = "none";
    button.innerHTML = '<i class="fas fa-chart-line"></i> Lihat Grafik';
    button.classList.replace('btn-danger', 'btn-primary');
  }
}

// Fungsi untuk merender grafik
function renderChart(variable, value, canvas) {
  const ctx = canvas.getContext('2d');

  // Definisi himpunan fuzzy trapezoid
  const sets = [
    { label: 'Rendah', params: [0, 0, 20, 40], color: '#ff6384', fillColor: 'rgba(255,99,132,0.1)' },
    { label: 'Sedang', params: [20, 40, 60, 80], color: '#ff9f40', fillColor: 'rgba(255,159,64,0.1)' },
    { label: 'Tinggi', params: [60, 80, 100, 100], color: '#4bc0c0', fillColor: 'rgba(75,192,192,0.1)' }
  ];

  // Hitung degree keanggotaan pada titik input
  const memberships = sets.map(set => {
    const [a, b, c, d] = set.params;
    let degree = 0;
    if (value <= a || value >= d) degree = 0;
    else if (value >= b && value <= c) degree = 1;
    else if (value > a && value < b) degree = (value - a) / (b - a);
    else degree = (d - value) / (d - c);
    return { ...set, degree: +degree.toFixed(2) };
  });

  // Siapkan data datasets
  const data = { datasets: [] };

  // Garis trapezoid penuh untuk setiap himpunan
  sets.forEach(set => {
    const [a, b, c, d] = set.params;
    const points =
      set.label === 'Rendah' ? [{x: a, y: 0}, {x: b, y: 1}, {x: c, y: 1}, {x: d, y: 0}] :
      set.label === 'Sedang' ? [{x: a, y: 0}, {x: b, y: 1}, {x: c, y: 1}, {x: d, y: 0}] :
                                [{x: a, y: 0}, {x: b, y: 1}, {x: c, y: 1}, {x: d, y: 0}];

    data.datasets.push({
      label: set.label,
      data: points,
      borderColor: set.color,
      backgroundColor: set.fillColor,
      tension: 0,
      fill: true,
      borderWidth: 2,
      pointRadius: 0,
      showLine: true
    });
  });

  // Garis horizontal & vertikal putus-putus pada degree > 0
  memberships.forEach(mem => {
    if (mem.degree > 0) {
      // Horizontal
      data.datasets.push({
        label: `${mem.label} (${mem.degree})`,
        data: [{x: 0, y: mem.degree}, {x: value, y: mem.degree}],
        borderColor: mem.color,
        borderDash: [6,6],
        borderWidth: 1,
        pointRadius: 0,
        showLine: true
      });
      // Vertikal
      data.datasets.push({
        label: '',
        data: [{x: value, y: 0}, {x: value, y: mem.degree}],
        borderColor: mem.color,
        borderDash: [6,6],
        borderWidth: 1,
        pointRadius: 0,
        showLine: true
      });
      // Titik intersection
      data.datasets.push({
        label: '',
        data: [{x: value, y: mem.degree}],
        borderColor: mem.color,
        backgroundColor: mem.color,
        pointRadius: 5,
        showLine: false
      });
    }
  });

  // Garis vertikal nilai variabel (agar selalu di atas)
  data.datasets.push({
    label: `Nilai ${variable} (${value})`,
    data: [{x: value, y: 0}, {x: value, y: 1}],
    borderColor: '#333',
    borderWidth: 2,
    pointRadius: 0,
    borderDash: [4,4],
    showLine: true
  });

  // Konfigurasi Chart sebagai line chart
  const config = {
    type: 'line',
    data: data,
    options: {
      scales: {
        x: { type: 'linear', min: 0, max: 100, title: { display: true, text: 'Nilai' } },
        y: { min: 0, max: 1, title: { display: true, text: 'Derajat Keanggotaan' } }
      },
      plugins: {
        legend: { position: 'top', labels: { filter: item => item.text !== '' } }
      }
    }
  };

  // Render dan simpan referensi
  window[`chart_${variable}`] = new Chart(ctx, config);
}

// Inisialisasi listener setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-chart').forEach(btn => {
    const variable = btn.id.replace('btn-', '');
    const header = btn.closest('.card-fuzzifikasi').querySelector('.card-header h5').textContent;
    const value = parseFloat(header.split(':')[1]);
    // Sembunyikan semua chart di awal
    document.getElementById(`chart-${variable}`).style.display = 'none';
    btn.addEventListener('click', () => toggleChart(variable, value));
  });
});
