/**
 * File: dashboard.js
 * Handle fetching data and rendering Chart.js visualizations
 */

document.addEventListener('DOMContentLoaded', function() {
    // Only run if we are on a dashboard page containing canvas elements
    const hasCharts = document.querySelectorAll('canvas.sk-chart').length > 0;
    if (!hasCharts) return;

    // Load Chart.js dynamically if not present
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = initDashboardCharts;
        document.head.appendChild(script);
    } else {
        initDashboardCharts();
    }

    function getThemeColors() {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        return {
            text: isDark ? '#d1d5db' : '#4b5563',
            grid: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
            bgMain: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b']
        };
    }

    function initDashboardCharts() {
        // Fetch data
        fetch('index.php?page=api_dashboard')
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderCharts(res.data);
                } else {
                    console.error('Failed to load dashboard data', res);
                }
            })
            .catch(err => console.error('Fetch error:', err));
    }

    function renderCharts(data) {
        const theme = getThemeColors();
        
        Chart.defaults.color = theme.text;
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // 0. Visualisasi Saldo Donut (Ormawa) - dari data attributes
        const danaCanvas = document.getElementById('danaChart');
        if (danaCanvas) {
            const sisa = parseInt(danaCanvas.dataset.sisa || 0);
            const digunakan = parseInt(danaCanvas.dataset.digunakan || 0);
            const total = parseInt(danaCanvas.dataset.total || 0);
            
            if (total === 0) {
                const parent = danaCanvas.parentElement;
                parent.innerHTML = '<div class="text-center text-muted p-5"><i class="bi bi-info-circle fs-2 mb-2"></i><br>Belum ada data dana yang dapat divisualisasikan.</div>';
            } else {
                new Chart(danaCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sisa Saldo Tersedia', 'Dana Terpakai & Diproses'],
                        datasets: [{
                            label: 'Distribusi Dana',
                            data: [sisa, digunakan],
                            backgroundColor: ['rgba(25, 135, 84, 0.8)', 'rgba(255, 193, 7, 0.8)'],
                            borderColor: ['rgba(25, 135, 84, 1)', 'rgba(255, 193, 7, 1)'],
                            borderWidth: 1,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, font: { size: 14 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // 1. Status Pengajuan (Doughnut) - Ormawa
        if (data.status_pengajuan && document.getElementById('chartStatusOrmawa')) {
            new Chart(document.getElementById('chartStatusOrmawa'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.status_pengajuan),
                    datasets: [{
                        data: Object.values(data.status_pengajuan),
                        backgroundColor: theme.bgMain,
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 2. Trend Pencairan (Bar) - Ormawa
        if (data.trend_pencairan && document.getElementById('chartTrendOrmawa')) {
            new Chart(document.getElementById('chartTrendOrmawa'), {
                type: 'bar',
                data: {
                    labels: data.trend_pencairan.labels,
                    datasets: [{
                        label: 'Dana Cair (Rp)',
                        data: data.trend_pencairan.data,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: theme.grid } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 3. Status Global (Pie) - Verifikator/Admin
        if (data.status_pengajuan_global && document.getElementById('chartStatusGlobal')) {
            new Chart(document.getElementById('chartStatusGlobal'), {
                type: 'pie',
                data: {
                    labels: Object.keys(data.status_pengajuan_global),
                    datasets: [{
                        data: Object.values(data.status_pengajuan_global),
                        backgroundColor: theme.bgMain,
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 4. Trend Pengajuan Masuk (Line) - Verifikator/Admin
        if (data.trend_pengajuan && document.getElementById('chartTrendGlobal')) {
            new Chart(document.getElementById('chartTrendGlobal'), {
                type: 'line',
                data: {
                    labels: data.trend_pengajuan.labels,
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: data.trend_pengajuan.data,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: theme.grid }, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 5. Top Ormawa (Horizontal Bar) - Verifikator/Admin
        if (data.top_ormawa && document.getElementById('chartTopOrmawa')) {
            new Chart(document.getElementById('chartTopOrmawa'), {
                type: 'bar',
                data: {
                    labels: data.top_ormawa.labels,
                    datasets: [{
                        label: 'Total Realisasi (Rp)',
                        data: data.top_ormawa.data,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, grid: { color: theme.grid } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }
    }
});