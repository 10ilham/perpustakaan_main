@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Dashboard Laporan</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a class="active">Laporan</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Peminjaman -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalPeminjaman }}</h2>
                        <p>Total Peminjaman</p>
                    </div>
                    <i class='bx bxs-book-bookmark icon'></i>
                </div>
            </div>
            <!-- Card Belum Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $belumKembali }}</h2>
                        <p>Belum Dikembalikan</p>
                    </div>
                    <i class='bx bxs-book-open icon'></i>
                </div>
            </div>
            <!-- Card Sudah Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $sudahKembali }}</h2>
                        <p>Sudah Dikembalikan</p>
                    </div>
                    <i class='bx bxs-book-alt icon'></i>
                </div>
            </div>
            <!-- Card Terlambat -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $terlambat }}</h2>
                        <p>Terlambat</p>
                    </div>
                    <i class='bx bxs-error-circle icon'></i>
                </div>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Menu Laporan</h3>
                </div>

                <!-- Menu Grid -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                    <!-- Card Belum Dikembalikan -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-book-open' style="font-size: 3rem; color: #ff6b6b; margin-bottom: 1rem;"></i>
                            <h4>Laporan Belum Dikembalikan</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat daftar buku yang belum
                                dikembalikan oleh peminjam</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.belum_kembali') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card Sudah Dikembalikan -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-book-alt' style="font-size: 3rem; color: #4ecdc4; margin-bottom: 1rem;"></i>
                            <h4>Laporan Sudah Dikembalikan</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat riwayat buku yang sudah
                                dikembalikan</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.sudah_kembali') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Bulanan -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Statistik Peminjaman</h3>
                    <div class="period-filter">
                        <select id="period-filter" class="filter-select">
                            <option value="day" selected>1 Hari</option>
                            <option value="week">1 Minggu</option>
                            <option value="month">1 Bulan</option>
                            <option value="6months">6 Bulan</option>
                            <option value="year">1 Tahun</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart Statistik Level -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Distribusi Peminjaman Per Level</h3>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="levelChart"></canvas>
                        <div id="levelChartRange" class="chart-range-info">
                            <!-- Period range will be updated by JavaScript -->
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="statusChart"></canvas>
                        <div id="statusChartRange" class="chart-range-info">
                            <!-- Period range will be updated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variables
        let monthlyChart;
        let levelChart;
        let statusChart;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadAllCharts('day'); // Default load 1 hari

            // Period filter event listener
            document.getElementById('period-filter').addEventListener('change', function() {
                const period = this.value;
                loadAllCharts(period);
            });

            // Handle window resize for responsive charts
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const period = document.getElementById('period-filter').value;
                    loadAllCharts(period);
                }, 250);
            });
        });

        function loadAllCharts(period) {
            // Show loading state
            showChartLoading();

            // Update time range descriptions
            updateTimeRangeDescriptions(period);

            // Load line chart data
            const lineChartPromise = fetch(`/laporan/chart-data?period=${period}`)
                .then(response => response.json());

            // Load pie chart data
            const pieChartPromise = fetch(`/laporan/pie-chart-data?period=${period}`)
                .then(response => response.json());

            // Wait for both requests to complete
            Promise.all([lineChartPromise, pieChartPromise])
                .then(([lineData, pieData]) => {
                    renderLineChart(lineData);
                    renderPieCharts(pieData, period); // Pass period to renderPieCharts
                    hideChartLoading();
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                    hideChartLoading();
                });
        }

        function updateTimeRangeDescriptions(period) {
            const now = new Date();
            let rangeText = '';

            // Indonesian month names
            const bulanIndonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            switch (period) {
                case 'day':
                    const today = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    rangeText = `Data hari ini: ${today}`;
                    break;

                case 'week':
                    const startOfWeek = new Date(now);
                    startOfWeek.setDate(now.getDate() - 6);
                    const endOfWeek = now;
                    rangeText =
                        `Data 7 hari terakhir: ${startOfWeek.getDate()} ${bulanIndonesia[startOfWeek.getMonth()]} - ${endOfWeek.getDate()} ${bulanIndonesia[endOfWeek.getMonth()]} ${endOfWeek.getFullYear()}`;
                    break;

                case 'month':
                    const currentMonth = bulanIndonesia[now.getMonth()];
                    const year = now.getFullYear();
                    rangeText = `Data bulan ${currentMonth} ${year}`;
                    break;

                case '6months':
                    const sixMonthsAgo = new Date(now);
                    sixMonthsAgo.setMonth(now.getMonth() - 5);
                    rangeText =
                        `Data 6 bulan terakhir: ${bulanIndonesia[sixMonthsAgo.getMonth()]} - ${bulanIndonesia[now.getMonth()]} ${now.getFullYear()}`;
                    break;

                case 'year':
                    rangeText = `Data tahun ${now.getFullYear()} (Januari - Desember)`;
                    break;

                default:
                    rangeText = 'Data periode yang dipilih';
            }

            // Update both chart range descriptions
            document.getElementById('levelChartRange').textContent = rangeText;
            document.getElementById('statusChartRange').textContent = rangeText;
        }

        function showChartLoading() {
            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach(container => {
                container.style.opacity = '0.6';
            });
        }

        function hideChartLoading() {
            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach(container => {
                container.style.opacity = '1';
            });
        }

        function renderLineChart(data) {
            // Destroy existing chart
            if (monthlyChart) {
                monthlyChart.destroy();
            }

            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.label),
                    datasets: [{
                        label: 'Dipinjam',
                        data: data.map(item => item.dipinjam),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }, {
                        label: 'Dikembalikan',
                        data: data.map(item => item.dikembalikan),
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Peminjaman dan Pengembalian'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        function renderPieCharts(data, period) {
            // Destroy existing charts
            if (levelChart) {
                levelChart.destroy();
            }
            if (statusChart) {
                statusChart.destroy();
            }

            // Determine chart titles based on period
            let levelChartTitle = 'Total Peminjaman Per Level';
            let statusChartTitle = 'Status Peminjaman';

            if (period === 'day') {
                levelChartTitle = 'Buku Sedang Dipinjam Per Level';
                statusChartTitle = 'Status Peminjaman Hari Ini';
            }

            // Chart Pie Level
            const levelCtx = document.getElementById('levelChart').getContext('2d');
            levelChart = new Chart(levelCtx, {
                type: 'pie',
                data: {
                    labels: data.levelData.map(item => item.level.charAt(0).toUpperCase() + item.level.slice(1)),
                    datasets: [{
                        data: data.levelData.map(item => item.total_peminjaman),
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)', // Siswa
                            'rgba(255, 205, 86, 0.8)', // Guru
                            'rgba(255, 99, 132, 0.8)' // Staff
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: levelChartTitle
                        }
                    }
                }
            });

            // Chart Status Peminjaman
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sedang Dipinjam', 'Sudah Dikembalikan'],
                    datasets: [{
                        data: [data.statusData.sedang_pinjam, data.statusData.sudah_kembali],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: statusChartTitle
                        }
                    }
                }
            });
        }
    </script>
@endsection
