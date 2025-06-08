function toggleChart(varName, xValue) {
        console.log('Rendering chart for', varName, fuzzData[varName]);
        const canvas = document.getElementById('membershipChart-' + varName);
        const ctx = canvas.getContext('2d');

        // Clear previous chart if exists
        if (charts[varName]) charts[varName].destroy();

        // 1) buat dataset fungsi keanggotaan
        const datasets = Object.entries(PARAMS).map(([name, p]) => ({
            label: name,
            data: p.map(xx => ({ x: xx, y: memb(p, xx) })),
            showLine: true,
            fill: false,
            borderColor: COLORS[name],
            pointRadius: 4,
            tension: 0
        }));

        // 2) garis bantu
        const mu = fuzzData[varName];
        const maxMu = Math.max(mu.Rendah, mu.Sedang, mu.Tinggi);
        datasets.push({
            label: 'μ max',
            data: [{ x: 0, y: maxMu }, { x: 100, y: maxMu }],
            borderDash: [6, 4],
            showLine: true,
            pointRadius: 0
        });
        datasets.push({
            label: 'x',
            data: [{ x: xValue, y: 0 }, { x: xValue, y: maxMu }],
            borderDash: [6, 4],
            showLine: true,
            pointRadius: 0
        });

        // 3) inisialisasi chart
        charts[varName] = new Chart(ctx, {
            type: 'scatter',
            data: { datasets },
            options: {
                parsing: false,
                scales: {
                    x: { min: 0, max: 100, title: { display: true, text: 'Nilai' } },
                    y: { min: 0, max: 1, title: { display: true, text: 'μ' } }
                },
                plugins: { legend: { position: 'top' } }
            }
        });
}
