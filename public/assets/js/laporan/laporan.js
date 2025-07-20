/**
 * Laporan.js - JavaScript Terpusat untuk Module Laporan
 * Author: Sistem Perpustakaan
 * Description: Berisi semua fungsi JavaScript untuk halaman laporan
 * Created: 2024
 *
 * FILE INI BERISI KUMPULAN SCRIPT DARI:
 * - index.blade.php (Chart Dashboard)
 * - belum_kembali.blade.php (Date Filter & DataTable)
 * - sudah_kembali.blade.php (Date Filter & DataTable)
 * - sanksi-belum-bayar.blade.php (DataTable & Export)
 * - sanksi-sudah-bayar.blade.php (DataTable & Export)
 */

// ===================================================================
// DARI: resources/views/laporan/index.blade.php
// FUNGSI: Grafik dashboard laporan (Line Chart, Pie Chart)
// ===================================================================

// Variabel global untuk menyimpan instance chart
let monthlyChart;
let levelChart;
let statusChart;

/**
 * Memuat semua data chart untuk dashboard laporan
 * DIGUNAKAN DI: resources/views/laporan/index.blade.php
 * @param {string} period - Periode waktu untuk data chart (day, week, month, 6months, year)
 */
function loadAllCharts(period) {
    console.log('loadAllCharts called with period:', period);
    showChartLoading();
    updateTimeRangeDescriptions(period);

    const lineChartPromise = fetch(`/laporan/chart-data?period=${period}`)
        .then(response => {
            console.log('Line chart response:', response);
            return response.json();
        });

    const pieChartPromise = fetch(`/laporan/pie-chart-data?period=${period}`)
        .then(response => {
            console.log('Pie chart response:', response);
            return response.json();
        });

    Promise.all([lineChartPromise, pieChartPromise])
        .then(([lineData, pieData]) => {
            console.log('Chart data received:', { lineData, pieData });
            renderLineChart(lineData);
            renderPieCharts(pieData, period);
            hideChartLoading();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            hideChartLoading();
        });
}

/**
 * Memperbarui deskripsi rentang waktu untuk chart dashboard
 * DIGUNAKAN DI: index.blade.php - Menampilkan periode waktu yang dipilih di bawah chart
 * @param {string} period - Periode waktu (day, week, month, 6months, year)
 */
function updateTimeRangeDescriptions(period) {
    const now = new Date();
    let rangeText = '';

    const bulanIndonesia = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    switch (period) {
        case 'day':
            const today = now.toLocaleDateString('id-ID', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            rangeText = `Data hari ini: ${today}`;
            break;
        case 'week':
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - 6);
            const endOfWeek = now;
            rangeText = `Data 7 hari terakhir: ${startOfWeek.getDate()} ${bulanIndonesia[startOfWeek.getMonth()]} - ${endOfWeek.getDate()} ${bulanIndonesia[endOfWeek.getMonth()]} ${endOfWeek.getFullYear()}`;
            break;
        case 'month':
            const currentMonth = bulanIndonesia[now.getMonth()];
            const year = now.getFullYear();
            rangeText = `Data bulan ${currentMonth} ${year}`;
            break;
        case '6months':
            const sixMonthsAgo = new Date(now);
            sixMonthsAgo.setMonth(now.getMonth() - 5);
            rangeText = `Data 6 bulan terakhir: ${bulanIndonesia[sixMonthsAgo.getMonth()]} - ${bulanIndonesia[now.getMonth()]} ${now.getFullYear()}`;
            break;
        case 'year':
            rangeText = `Data tahun ${now.getFullYear()} (Januari - Desember)`;
            break;
        default:
            rangeText = 'Data periode yang dipilih';
    }

    const levelChartRange = document.getElementById('levelChartRange');
    const statusChartRange = document.getElementById('statusChartRange');

    if (levelChartRange) levelChartRange.textContent = rangeText;
    if (statusChartRange) statusChartRange.textContent = rangeText;
}

/**
 * Menampilkan indikator loading pada chart dashboard
 * DIGUNAKAN DI: index.blade.php - Membuat chart opacity 60% saat loading data
 */
function showChartLoading() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
        container.style.opacity = '0.6';
    });
}

/**
 * Menyembunyikan indikator loading pada chart dashboard
 * DIGUNAKAN DI: index.blade.php - Mengembalikan opacity chart ke normal setelah data dimuat
 */
function hideChartLoading() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
        container.style.opacity = '1';
    });
}

/**
 * Membuat chart garis untuk trend peminjaman dan pengembalian
 * DIGUNAKAN DI: index.blade.php - Chart utama dashboard yang menampilkan trend waktu
 * @param {Array} data - Data chart berisi label, jumlah dipinjam, dan dikembalikan
 */
function renderLineChart(data) {
    console.log('renderLineChart called with data:', data);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }

    if (monthlyChart) {
        monthlyChart.destroy();
    }

    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    console.log('Creating line chart...');

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
                    ticks: { stepSize: 1 }
                },
                x: {
                    ticks: { maxRotation: 45 }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    console.log('Line chart created successfully');
}

/**
 * Membuat chart pie untuk level dan status peminjaman
 * DIGUNAKAN DI: index.blade.php - Dua chart pie di sebelah kanan dashboard
 * @param {Object} data - Data chart berisi levelData dan statusData
 * @param {string} period - Periode waktu untuk menyesuaikan judul chart
 */
function renderPieCharts(data, period) {
    console.log('renderPieCharts called with data:', data, 'period:', period);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded!');
        return;
    }

    if (levelChart) levelChart.destroy();
    if (statusChart) statusChart.destroy();

    let levelChartTitle = 'Total Peminjaman Per Level';
    let statusChartTitle = 'Status Peminjaman';

    if (period === 'day') {
        levelChartTitle = 'Buku Sedang Dipinjam Per Level';
        statusChartTitle = 'Status Peminjaman Hari Ini';
    }

    // Chart Pie Level - Membuat chart pie untuk distribusi per level
    const levelCtx = document.getElementById('levelChart').getContext('2d');
    console.log('Creating level chart...');

    levelChart = new Chart(levelCtx, {
        type: 'pie',
        data: {
            labels: data.levelData.map(item => item.level.charAt(0).toUpperCase() + item.level.slice(1)),
            datasets: [{
                data: data.levelData.map(item => item.total_peminjaman),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
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

    // Chart Status Peminjaman - Membuat chart doughnut untuk status peminjaman
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    console.log('Creating status chart...');

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

    console.log('Pie charts created successfully');
}

/**
 * Inisialisasi chart dashboard saat halaman dimuat
 * DIGUNAKAN DI: index.blade.php - Event listener untuk memuat chart dan filter periode
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah ini halaman dashboard laporan - Verifikasi keberadaan elemen chart untuk inisialisasi
    const monthlyChartElement = document.getElementById('monthlyChart');
    const periodFilter = document.getElementById('period-filter');

    console.log('Dashboard chart initialization:', { monthlyChartElement, periodFilter });

    if (monthlyChartElement && periodFilter) {
        console.log('Loading charts...');
        // Delay sedikit untuk memastikan Chart.js sudah fully loaded - Tunggu Chart.js selesai dimuat
        setTimeout(function() {
            loadAllCharts('day'); // Default load 1 hari - Muat chart dengan periode default hari ini
        }, 100);

        // Period filter event listener - Event listener untuk perubahan filter periode
        periodFilter.addEventListener('change', function() {
            const period = this.value;
            console.log('Period changed to:', period);
            loadAllCharts(period);
        });

        // Handle window resize for responsive charts - Tangani resize window untuk chart responsive
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const period = document.getElementById('period-filter').value;
                loadAllCharts(period);
            }, 250);
        });
    }
});

// ===================================================================
// DARI: resources/views/laporan/belum_kembali.blade.php & sudah_kembali.blade.php
// FUNGSI: Kontrol filter tanggal dan tombol reset
// ===================================================================

/**
 * Kontrol input filter tanggal - Memastikan tanggal akhir tidak lebih awal dari tanggal mulai
 * DIGUNAKAN DI: belum_kembali.blade.php, sudah_kembali.blade.php, sanksi-belum-bayar.blade.php, sanksi-sudah-bayar.blade.php
 */
document.addEventListener('DOMContentLoaded', function() {
    var tanggalMulaiInput = document.getElementById('tanggal_mulai');
    var tanggalSelesaiInput = document.getElementById('tanggal_selesai');
    var tanggalAkhirInput = document.getElementById('tanggal_akhir'); // untuk sanksi - Untuk halaman sanksi

    // Pilih input akhir yang ada (tanggal_selesai atau tanggal_akhir) - Deteksi input tanggal akhir yang tersedia
    var endDateInput = tanggalSelesaiInput || tanggalAkhirInput;

    if (tanggalMulaiInput && endDateInput) {
        // Set minimum date for end date - Atur tanggal minimum untuk input tanggal akhir
        if (tanggalMulaiInput.value) {
            endDateInput.setAttribute('min', tanggalMulaiInput.value);
        }

        // Update minimum end date when start date changes - Perbarui tanggal minimum saat tanggal mulai berubah
        tanggalMulaiInput.addEventListener('change', function() {
            if (this.value) {
                endDateInput.setAttribute('min', this.value);

                // Reset end date if it's earlier than start date - Reset tanggal akhir jika lebih awal dari tanggal mulai
                if (endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
            } else {
                endDateInput.removeAttribute('min');
            }
        });
    }
});

/**
 * Kontrol tombol reset - Menampilkan/menyembunyikan tombol reset berdasarkan filter aktif
 * DIGUNAKAN DI: Semua halaman laporan - Memonitor perubahan filter dan mengatur tampilan tombol reset
 */
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Mengecek apakah ada filter aktif di URL
     * DIGUNAKAN DI: Semua halaman laporan - Untuk menampilkan/menyembunyikan tombol reset
     * @returns {boolean} True jika ada filter aktif
     */
    function hasActiveFilter() {
        const urlParams = new URLSearchParams(window.location.search);

        // Check all possible filter parameters - Cek semua parameter filter yang mungkin ada
        if (urlParams.get('tanggal_mulai')) return true;
        if (urlParams.get('tanggal_selesai')) return true;
        if (urlParams.get('tanggal_akhir')) return true;
        if (urlParams.get('status')) return true;
        if (urlParams.get('jenis_sanksi')) return true;
        if (urlParams.get('level')) return true;

        return false;
    }

    /**
     * Mengatur tampilan tombol reset berdasarkan status filter
     * DIGUNAKAN DI: Semua halaman laporan - Menampilkan tombol reset jika ada filter aktif
     */
    function toggleResetButton() {
        // Deteksi user admin dengan mengecek elemen level filter - Deteksi admin berdasarkan elemen filter level
        var isAdmin = document.getElementById('level') !== null;
        var resetBtn = document.getElementById(isAdmin ? 'resetBtn' : 'resetBtnNonAdmin');

        if (resetBtn) {
            if (hasActiveFilter()) {
                resetBtn.style.display = 'inline-flex';
            } else {
                resetBtn.style.display = 'none';
            }
        }
    }

    toggleResetButton();
});

// ===================================================================
// DARI: Semua halaman dengan DataTable (belum_kembali, sudah_kembali, sanksi-belum-bayar, sanksi-sudah-bayar)
// FUNGSI: Inisialisasi DataTable dengan tombol export dan konfigurasi responsive
// ===================================================================

$(document).ready(function() {
    // Cek apakah halaman memiliki DataTable - Pastikan elemen DataTable ada sebelum inisialisasi
    if (!$('#dataTableExport').length) {
        return;
    }

    // Deteksi admin dan halaman - Tentukan konfigurasi berdasarkan role user dan halaman saat ini
    var isAdmin = $('#level').length > 0;
    var currentPath = window.location.pathname;
    var pageType = getPageType(currentPath);

    // Get responsive priorities dan export buttons - Dapatkan konfigurasi responsif dan tombol export
    var responsivePriorities = getResponsivePriorities(isAdmin, pageType);
    var exportButtons = isAdmin ? getExportButtons(pageType) : [];

    // DataTable configuration - Konfigurasi dasar DataTable
    var dataTableConfig = {
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: responsivePriorities
    };

    // Add export configuration for admin - Tambahkan konfigurasi export khusus untuk admin
    if (isAdmin && exportButtons.length > 0) {
        dataTableConfig.dom = '<"export-buttons-container"B>lfrtip';
        dataTableConfig.language = {
            url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
        };
        dataTableConfig.buttons = exportButtons;
    }

    $('#dataTableExport').DataTable(dataTableConfig);

    // Global function untuk export Word - Fungsi global yang dapat diakses dari tombol export Word
    window.exportToWord = function(dt) {
        exportTableToWord(dt, isAdmin, pageType);
    };
});

/**
 * Menentukan tipe halaman berdasarkan URL path
 * DIGUNAKAN DI: Semua halaman laporan - Untuk konfigurasi DataTable yang sesuai
 * @param {string} currentPath - Path URL saat ini
 * @returns {string} Tipe halaman (belum_kembali, sudah_kembali, sanksi_belum_bayar, sanksi_sudah_bayar, default)
 */
function getPageType(currentPath) {
    if (currentPath.includes('belum-kembali')) return 'belum_kembali';
    if (currentPath.includes('sudah-kembali')) return 'sudah_kembali';
    if (currentPath.includes('sanksi-belum-bayar')) return 'sanksi_belum_bayar';
    if (currentPath.includes('sanksi-sudah-bayar')) return 'sanksi_sudah_bayar';
    if (currentPath.includes('buku-log') || currentPath.includes('buku_log')) return 'buku_log';
    if (currentPath.includes('blacklist')) return 'blacklist';
    return 'default';
}

/**
 * Mendapatkan konfigurasi responsive priorities untuk DataTable
 * DIGUNAKAN DI: Semua halaman laporan - Mengatur kolom mana yang diprioritaskan saat responsive
 * @param {boolean} isAdmin - Apakah user adalah admin
 * @param {string} pageType - Tipe halaman laporan
 * @returns {Array} Array konfigurasi responsive priorities
 */
function getResponsivePriorities(isAdmin, pageType) {
    switch (pageType) {
        case 'belum_kembali':
            return isAdmin ? [
                { responsivePriority: 1, targets: [0, 1, 4, 7] },
                { responsivePriority: 2, targets: [8] },
                { orderable: false, targets: [-1] }
            ] : [
                { responsivePriority: 1, targets: [0, 1, 4] },
                { responsivePriority: 2, targets: [5] },
                { orderable: false, targets: [-1] }
            ];
        case 'sudah_kembali':
            return isAdmin ? [
                { responsivePriority: 1, targets: [0, 1, 4, 8] },
                { responsivePriority: 2, targets: [9] },
                { orderable: false, targets: [-1] }
            ] : [
                { responsivePriority: 1, targets: [0, 1, 5] },
                { responsivePriority: 2, targets: [6] },
                { orderable: false, targets: [-1] }
            ];
        case 'sanksi_belum_bayar':
        case 'sanksi_sudah_bayar':
            return isAdmin ? [
                { responsivePriority: 1, targets: [0, 2, 4, 9] },
                { responsivePriority: 2, targets: [10] },
                { orderable: false, targets: [-1] }
            ] : [
                { responsivePriority: 1, targets: [0, 2, 6] },
                { responsivePriority: 2, targets: [7] },
                { orderable: false, targets: [-1] }
            ];
        case 'blacklist':
            return [
                { responsivePriority: 1, targets: [0, 1, 3, 7, 8] }, // No, Nama, Level, Status, Aksi
                { responsivePriority: 2, targets: [4, 5, 6] }, // Jumlah Pembatalan, Tanggal Blacklist, Tanggal Berakhir
                { responsivePriority: 3, targets: [2] } // Email
            ];
        case 'buku_log':
            return [
                { responsivePriority: 1, targets: [0, 1, 3, 4, 7] },
                { responsivePriority: 2, targets: [5, 6] },
                { orderable: false, targets: [7] }
            ];
        default:
            return [{ orderable: false, targets: [-1] }];
    }
}

/**
 * Membuat konfigurasi tombol export untuk DataTable admin
 * DIGUNAKAN DI: Semua halaman laporan (khusus admin) - Tombol Copy, CSV, Excel, Word, PDF, Print
 * @param {string} pageType - Tipe halaman untuk penamaan file export
 * @returns {Array} Array konfigurasi tombol export
 */
function getExportButtons(pageType) {
    const currentDate = new Date().toLocaleDateString('id-ID').replace(/\//g, '-');
    let filePrefix = 'Laporan_';

    switch (pageType) {
        case 'belum_kembali': filePrefix += 'Belum_Dikembalikan_'; break;
        case 'sudah_kembali': filePrefix += 'Sudah_Dikembalikan_'; break;
        case 'sanksi_belum_bayar': filePrefix += 'Sanksi_Belum_Bayar_'; break;
        case 'sanksi_sudah_bayar': filePrefix += 'Sanksi_Sudah_Bayar_'; break;
        case 'blacklist': filePrefix += 'User_Blacklist_'; break;
        case 'buku_log': filePrefix += 'Buku_Masuk_Keluar_'; break;
        default: filePrefix = 'Laporan_';
    }

    const exportColumns = (pageType.includes('sanksi')) ? ':visible' : (pageType === 'blacklist') ? ':not(:last-child)' : ':not(:last-child)';

    return [
        {
            extend: 'copy',
            text: '<i class="bx bx-copy"></i><span>Copy</span>',
            className: 'btn btn-outline-primary btn-sm export-btn',
            exportOptions: { columns: exportColumns }
        },
        {
            extend: 'csv',
            text: '<i class="bx bx-file"></i><span>CSV</span>',
            className: 'btn btn-outline-success btn-sm export-btn',
            filename: filePrefix + currentDate,
            exportOptions: { columns: exportColumns }
        },
        {
            extend: 'excel',
            text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
            className: 'btn btn-outline-success btn-sm export-btn',
            filename: filePrefix + currentDate,
            exportOptions: { columns: exportColumns }
        },
        {
            text: '<i class="bx bxs-file-doc"></i><span>Word</span>',
            className: 'btn btn-outline-info btn-sm export-btn',
            action: function(e, dt, button, config) {
                exportToWord(dt);
            }
        },
        {
            extend: 'pdf',
            text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
            className: 'btn btn-outline-danger btn-sm export-btn',
            filename: filePrefix + currentDate,
            orientation: 'landscape',
            exportOptions: { columns: exportColumns }
        },
        {
            extend: 'print',
            text: '<i class="bx bx-printer"></i><span>Print</span>',
            className: 'btn btn-outline-warning btn-sm export-btn',
            exportOptions: { columns: exportColumns }
        }
    ];
}

// ===================================================================
// DARI: Semua halaman dengan export Word (belum_kembali, sudah_kembali, sanksi-belum-bayar, sanksi-sudah-bayar)
// FUNGSI: Export tabel ke dokumen Word dengan formatting khusus per halaman
// ===================================================================

/**
 * Export data DataTable ke dokumen Word dengan styling dan format khusus
 * DIGUNAKAN DI: Semua halaman laporan (khusus admin) - Tombol export Word di DataTable
 * @param {Object} dt - Instance DataTable
 * @param {boolean} isAdmin - Apakah user adalah admin
 * @param {string} pageType - Tipe halaman untuk konfigurasi export
 */
function exportTableToWord(dt, isAdmin, pageType) {
    const exportColumns = (pageType.includes('sanksi')) ? ':visible' : (pageType === 'blacklist') ? ':not(:last-child)' : ':not(:last-child)';
    const data = dt.buttons.exportData({ columns: exportColumns });

    var columnClasses, documentTitle, fileName;
    const currentDate = new Date().toLocaleDateString('id-ID').replace(/\//g, '-');

    // Configuration per page type - Konfigurasi spesifik berdasarkan tipe halaman
    switch (pageType) {
        case 'belum_kembali':
            if (isAdmin) {
                columnClasses = ['col-no', 'col-nama', 'col-email', 'col-level', 'col-buku', 'col-tgl-pinjam', 'col-tgl-kembali', 'col-status'];
                documentTitle = 'Laporan Buku Belum Dikembalikan';
                fileName = `Laporan_Belum_Dikembalikan_${currentDate}.doc`;
            } else {
                columnClasses = ['col-no', 'col-buku', 'col-tgl-pinjam', 'col-tgl-kembali', 'col-status'];
                documentTitle = 'Riwayat Peminjaman Saya (Belum Dikembalikan)';
                fileName = `Riwayat_Belum_Dikembalikan_${currentDate}.doc`;
            }
            break;
        case 'sudah_kembali':
            if (isAdmin) {
                columnClasses = ['col-no', 'col-nama', 'col-email', 'col-level', 'col-buku', 'col-tgl-pinjam', 'col-tgl-kembali', 'col-tgl-dikembalikan', 'col-status'];
                documentTitle = 'Laporan Buku Sudah Dikembalikan';
                fileName = `Laporan_Sudah_Dikembalikan_${currentDate}.doc`;
            } else {
                columnClasses = ['col-no', 'col-buku', 'col-tgl-pinjam', 'col-tgl-kembali', 'col-tgl-dikembalikan', 'col-status'];
                documentTitle = 'Laporan Riwayat Peminjaman Anda (Sudah Dikembalikan)';
                fileName = `Riwayat_Sudah_Dikembalikan_${currentDate}.doc`;
            }
            break;
        case 'sanksi_belum_bayar':
            if (isAdmin) {
                columnClasses = ['col-no', 'col-tanggal', 'col-nama', 'col-level', 'col-buku', 'col-jenis', 'col-hari', 'col-denda-keterlambatan', 'col-denda-kerusakan', 'col-total', 'col-keterangan'];
                documentTitle = 'Laporan Sanksi Belum Bayar';
                fileName = `Laporan_Sanksi_Belum_Bayar_${currentDate}.doc`;
            } else {
                columnClasses = ['col-no', 'col-tanggal', 'col-buku', 'col-jenis', 'col-hari', 'col-denda-keterlambatan', 'col-denda-kerusakan', 'col-total', 'col-keterangan'];
                documentTitle = 'Sanksi Belum Bayar Anda';
                fileName = `Sanksi_Belum_Bayar_${currentDate}.doc`;
            }
            break;
        case 'sanksi_sudah_bayar':
            if (isAdmin) {
                columnClasses = ['col-no', 'col-tanggal', 'col-nama', 'col-level', 'col-buku', 'col-jenis', 'col-hari', 'col-denda-keterlambatan', 'col-denda-kerusakan', 'col-total', 'col-keterangan'];
                documentTitle = 'Laporan Sanksi Sudah Bayar';
                fileName = `Laporan_Sanksi_Sudah_Bayar_${currentDate}.doc`;
            } else {
                columnClasses = ['col-no', 'col-tanggal', 'col-buku', 'col-jenis', 'col-hari', 'col-denda-keterlambatan', 'col-denda-kerusakan', 'col-total', 'col-keterangan'];
                documentTitle = 'Sanksi Sudah Bayar Anda';
                fileName = `Sanksi_Sudah_Bayar_${currentDate}.doc`;
            }
            break;
        case 'blacklist':
            columnClasses = ['col-no', 'col-nama', 'col-email', 'col-level', 'col-jumlah-pembatalan', 'col-tgl-blacklist', 'col-tgl-berakhir', 'col-status', 'col-aksi'];
            documentTitle = 'Laporan User Blacklist';
            fileName = `Laporan_User_Blacklist_${currentDate}.doc`;
            break;
        default:
            columnClasses = [];
            documentTitle = 'Laporan Data';
            fileName = `Laporan_${currentDate}.doc`;
    }

    // Create HTML content - Buat konten HTML untuk dokumen Word
    let htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:w="urn:schemas-microsoft-com:office:word"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <title>${documentTitle}</title>
            <!--[if gte mso 9]>
            <xml>
                <w:WordDocument>
                    <w:View>Print</w:View>
                    <w:Zoom>90</w:Zoom>
                    <w:Orientation>Landscape</w:Orientation>
                </w:WordDocument>
            </xml>
            <![endif]-->
            <style>
                @page { size: A4 landscape; margin: 0.5in; }
                body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
                .header { text-align: center; margin-bottom: 15px; }
                .header h2 { font-size: 14px; margin: 0 0 5px 0; }
                .date { text-align: center; margin-bottom: 15px; color: #666; font-size: 9px; }
                table { border-collapse: collapse; width: 100%; font-size: 8px; table-layout: fixed; }
                th, td { border: 1px solid #ddd; padding: 4px; text-align: left; word-wrap: break-word; overflow-wrap: break-word; vertical-align: top; }
                th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
                ${getColumnWidthStyles(pageType, isAdmin)}
                .text-center { text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>${documentTitle}</h2>
            </div>
            <div class="date">
                <p>Data per tanggal ${new Date().toLocaleDateString('id-ID')}</p>
            </div>
            <table>
                <thead>
                    <tr>`;

    // Add headers - Tambahkan header tabel
    data.header.forEach(function(header, index) {
        const className = columnClasses[index] || '';
        htmlContent += `<th class="${className}">${header}</th>`;
    });

    htmlContent += `
                    </tr>
                </thead>
                <tbody>`;

    // Add data rows - Tambahkan baris data tabel
    data.body.forEach(function(row) {
        htmlContent += '<tr>';
        row.forEach(function(cell, index) {
            let cleanCell = cell.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
            const className = columnClasses[index] || '';
            htmlContent += `<td class="${className}">${cleanCell}</td>`;
        });
        htmlContent += '</tr>';
    });

    htmlContent += `
                </tbody>
            </table>
        </body>
        </html>`;

    // Create and download - Buat blob file dan trigger download
    const blob = new Blob([htmlContent], { type: 'application/msword' });

    if (typeof saveAs !== 'undefined') {
        saveAs(blob, fileName);
    } else {
        // Fallback - Metode alternatif jika saveAs tidak tersedia
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }
}

/**
 * Mendapatkan CSS styling untuk lebar kolom tabel Word export
 * DIGUNAKAN DI: Fungsi exportTableToWord - Styling khusus per halaman dan role user
 * @param {string} pageType - Tipe halaman laporan
 * @param {boolean} isAdmin - Apakah user adalah admin
 * @returns {string} CSS styling untuk lebar kolom
 */
function getColumnWidthStyles(pageType, isAdmin) {
    switch (pageType) {
        case 'belum_kembali':
            return isAdmin ? `
                .col-no { width: 5%; } .col-nama { width: 15%; } .col-email { width: 18%; }
                .col-level { width: 8%; } .col-buku { width: 20%; } .col-tgl-pinjam { width: 12%; }
                .col-tgl-kembali { width: 12%; } .col-status { width: 10%; }` : `
                .col-no { width: 8%; } .col-buku { width: 35%; } .col-tgl-pinjam { width: 19%; }
                .col-tgl-kembali { width: 19%; } .col-status { width: 19%; }`;
        case 'sudah_kembali':
            return isAdmin ? `
                .col-no { width: 4%; } .col-nama { width: 12%; } .col-email { width: 15%; }
                .col-level { width: 7%; } .col-buku { width: 18%; } .col-tgl-pinjam { width: 11%; }
                .col-tgl-kembali { width: 11%; } .col-tgl-dikembalikan { width: 11%; } .col-status { width: 11%; }` : `
                .col-no { width: 8%; } .col-buku { width: 30%; } .col-tgl-pinjam { width: 15%; }
                .col-tgl-kembali { width: 15%; } .col-tgl-dikembalikan { width: 15%; } .col-status { width: 17%; }`;
        case 'sanksi_belum_bayar':
        case 'sanksi_sudah_bayar':
            return isAdmin ? `
                .col-no { width: 4%; } .col-tanggal { width: 12%; } .col-nama { width: 15%; }
                .col-level { width: 7%; } .col-buku { width: 15%; } .col-jenis { width: 12%; }
                .col-hari { width: 8%; } .col-denda-keterlambatan { width: 10%; } .col-denda-kerusakan { width: 10%; }
                .col-total { width: 10%; } .col-keterangan { width: 7%; }` : `
                .col-no { width: 6%; } .col-tanggal { width: 15%; } .col-buku { width: 20%; }
                .col-jenis { width: 15%; } .col-hari { width: 10%; } .col-denda-keterlambatan { width: 12%; }
                .col-denda-kerusakan { width: 12%; } .col-total { width: 12%; } .col-keterangan { width: 8%; }`;
        case 'buku_log':
            return `
                .col-no { width: 5%; } .col-tanggal { width: 10%; } .col-kode { width: 12%; }
                .col-judul { width: 25%; } .col-tipe { width: 8%; } .col-jumlah { width: 8%; }
                .col-alasan { width: 22%; } .col-aksi { width: 10%; }`;
        case 'blacklist':
            return `
                .col-no { width: 5%; } .col-nama { width: 16%; } .col-email { width: 18%; }
                .col-level { width: 8%; } .col-jumlah-pembatalan { width: 10%; } .col-tgl-blacklist { width: 13%; }
                .col-tgl-berakhir { width: 13%; } .col-status { width: 9%; } .col-aksi { width: 8%; }`;
        default:
            return '.col-no { width: 10%; }';
    }
}

// ===================================================================
// END OF LAPORAN.JS
// ===================================================================
