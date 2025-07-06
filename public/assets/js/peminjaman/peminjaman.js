/**
 * PEMINJAMAN.JS
 * File JavaScript terpusat untuk semua fitur peminjaman buku
 *
 * Berisi semua logic JavaScript yang dipindahkan dari:
 * - index.blade.php (Halaman daftar peminjaman)
 * - detail.blade.php (Halaman detail peminjaman)
 * - form.blade.php (Halaman form peminjaman)
 * - manual.blade.php (Halaman form peminjaman manual)
 */

// ============================================================================
// FUNGSI UNTUK HALAMAN INDEX (index.blade.php)
// ============================================================================

/**
 * Fungsi untuk halaman index peminjaman
 * Menangani: tombol aksi, filter tanggal, export data, DataTable
 */
function initPeminjamanIndex() {
    // Handler untuk tombol pengembalian - menggunakan modal sanksi
    document.querySelectorAll('.btn-success-peminjaman').forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data dari atribut data-*
            const peminjamanData = {
                peminjaman_id: this.getAttribute('data-peminjaman-id'),
                judul_buku: this.getAttribute('data-judul-buku'),
                nama_peminjam: this.getAttribute('data-nama-peminjam'),
                tanggal_pinjam: this.getAttribute('data-tanggal-pinjam'),
                tanggal_kembali: this.getAttribute('data-tanggal-kembali'),
                harga_buku: parseInt(this.getAttribute('data-harga-buku')) || 0
            };

            // Panggil fungsi untuk menampilkan modal sanksi
            if (typeof window.showSanksiModal === 'function') {
                window.showSanksiModal(peminjamanData);
            } else {
                console.error('showSanksiModal function not found');
            }
        });
    });

    // Event delegation untuk tombol yang dimuat dinamis (DataTable)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-success-peminjaman')) {
            const button = e.target.closest('.btn-success-peminjaman');

            const peminjamanData = {
                peminjaman_id: button.getAttribute('data-peminjaman-id'),
                judul_buku: button.getAttribute('data-judul-buku'),
                nama_peminjam: button.getAttribute('data-nama-peminjam'),
                tanggal_pinjam: button.getAttribute('data-tanggal-pinjam'),
                tanggal_kembali: button.getAttribute('data-tanggal-kembali'),
                harga_buku: parseInt(button.getAttribute('data-harga-buku')) || 0
            };

            if (typeof window.showSanksiModal === 'function') {
                window.showSanksiModal(peminjamanData);
            } else {
                console.error('showSanksiModal function not found');
            }
        }
    });

    // Handler untuk tombol hapus
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            document.getElementById('delete-form').setAttribute('action', actionUrl);
        });
    });

    // Handler untuk tombol pengambilan
    document.querySelectorAll('.btn-success-pengambilan').forEach(button => {
        button.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            document.getElementById('pengambilan-form').setAttribute('action', actionUrl);
        });
    });

    // Handler untuk date inputs - Filter tanggal
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            if (startDate.value) {
                endDate.min = startDate.value;
            }
        });

        endDate.addEventListener('change', function() {
            if (endDate.value) {
                startDate.max = endDate.value;
            } else {
                // Jika end date dikosongkan, hapus batasan max pada start date
                startDate.removeAttribute('max');
            }
        });

        if (startDate.value) {
            endDate.min = startDate.value;
        }

        if (endDate.value) {
            startDate.max = endDate.value;
        }
    }

    // Inisialisasi fungsi export untuk admin
    if (document.querySelector('#exportAllToExcel')) {
        initExportFunctions();
    }

    // Inisialisasi DataTable untuk admin
    if (document.querySelector('#tableSiswa') || document.querySelector('#tableGuru') || document.querySelector('#tableStaff')) {
        initDataTables();
    }
}

/**
 * Fungsi untuk halaman index - Export data semua tabel
 * Menangani: export ke Excel, Word, PDF, CSV, dan Print
 */
function initExportFunctions() {
    // Fungsi Global Export untuk Gabungan Semua Tabel
    function getAllTableData() {
        const allData = [];
        let no = 1;

        // Fungsi helper untuk membersihkan teks dari HTML
        const getCleanText = (element) => {
            if (typeof element === 'string') {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = element;
                return tempDiv.textContent || tempDiv.innerText || element;
            }
            return element;
        };

        // Fungsi untuk mengumpulkan data dari tabel
        const collectDataFromTable = (tableId, userLevel) => {
            if ($.fn.DataTable.isDataTable(tableId)) {
                const table = $(tableId).DataTable();
                table.rows().every(function() {
                    const row = this.data();
                    const rowData = [
                        no++,
                        getCleanText(row[1]), // No. Peminjaman
                        getCleanText(row[2]), // Judul Buku
                        getCleanText(row[3]), // Nama Peminjam
                        getCleanText(row[4]), // Tanggal Pinjam
                        getCleanText(row[5]), // Tanggal Batas Kembali
                        getCleanText(row[6]), // Tanggal Pengembalian
                        getCleanText(row[7]), // Status
                        getCleanText(row[8]), // Catatan
                        userLevel
                    ];
                    allData.push(rowData);
                });
            }
        };

        // Kumpulkan data dari semua tabel
        collectDataFromTable('#tableSiswa', 'Siswa');
        collectDataFromTable('#tableGuru', 'Guru');
        collectDataFromTable('#tableStaff', 'Staff');

        return allData;
    }

    // Konfigurasi dan Data Export
    const exportConfig = {
        headers: ['No', 'No. Peminjaman', 'Judul Buku', 'Nama Peminjam', 'Tanggal Pinjam',
            'Tanggal Batas Kembali', 'Tanggal Pengembalian', 'Status', 'Catatan', 'Level'
        ],
        fileName: 'Data_Peminjaman_Semua_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
        dateExport: new Date().toLocaleString('id-ID')
    };

    // Validasi Data Export
    const validateExportData = () => {
        const data = getAllTableData();
        if (data.length === 0) {
            alert('Tidak ada data untuk diekspor!');
            return null;
        }
        return data;
    };

    // Export ke Excel
    $('#exportAllToExcel').on('click', function() {
        const data = validateExportData();
        if (!data) return;

        const ws = XLSX.utils.aoa_to_sheet([exportConfig.headers, ...data]);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Data Peminjaman Semua');
        XLSX.writeFile(wb, exportConfig.fileName + '.xlsx');
    });

    // Export ke Word dengan format yang diperbaiki
    $('#exportAllToWord').on('click', function() {
        const data = validateExportData();
        if (!data) return;

        // Template HTML untuk Word dengan orientasi landscape
        const wordTemplate = `
        <!DOCTYPE html>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
        <head>
            <meta charset="utf-8">
            <title>Data Peminjaman Semua Anggota</title>
            <!--[if gte mso 9]>
            <xml><w:WordDocument><w:View>Print</w:View><w:Zoom>90</w:Zoom><w:Orientation>Landscape</w:Orientation></w:WordDocument></xml>
            <![endif]-->
            <style>
                @page { size: A4 landscape; margin: 0.5in; }
                body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; }
                .header { text-align: center; margin-bottom: 15px; }
                .header h2 { font-size: 14px; margin: 0 0 5px 0; }
                .date { text-align: center; margin-bottom: 15px; color: #666; font-size: 9px; }
                table { border-collapse: collapse; width: 100%; font-size: 8px; table-layout: fixed; }
                th, td { border: 1px solid #ddd; padding: 4px; text-align: left; word-wrap: break-word; vertical-align: top; }
                th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
            </style>
        </head>
        <body>
            <div class="header"><h2>Data Peminjaman Semua Anggota</h2></div>
            <div class="date"><p>Total Data: ${data.length} Peminjaman - Tanggal Export: ${exportConfig.dateExport}</p></div>
            <table><thead><tr>${exportConfig.headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
            <tbody>${data.map(row => `<tr>${row.map(cell => `<td>${(cell || '').toString().replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim() || '-'}</td>`).join('')}</tr>`).join('')}</tbody>
            </table>
        </body>
        </html>`;

        // Blob buat word kegunaan untuk menyimpan file
        const blob = new Blob([wordTemplate], {
            type: 'application/msword'
        });
        saveAs(blob, exportConfig.fileName + '.doc');
    });

    // Export ke PDF
    $('#exportAllToPDF').on('click', function() {
        const data = validateExportData();
        if (!data) return;

        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape

        // Header PDF
        doc.setFontSize(16);
        doc.text('Data Peminjaman Semua Anggota', doc.internal.pageSize.getWidth() / 2, 15, {
            align: 'center'
        });
        doc.setFontSize(10);
        doc.text(`Total Data: ${data.length} Peminjaman`, doc.internal.pageSize.getWidth() / 2,
            25, {
                align: 'center'
            });
        doc.text(`Tanggal Export: ${exportConfig.dateExport}`, doc.internal.pageSize.getWidth() / 2,
            30, {
                align: 'center'
            });

        // Tabel PDF
        doc.autoTable({
            head: [exportConfig.headers],
            body: data,
            startY: 35,
            styles: {
                fontSize: 8
            },
            headStyles: {
                fillColor: [66, 139, 202]
            },
            margin: {
                top: 35
            }
        });

        doc.save(exportConfig.fileName + '.pdf');
    });

    // Export ke CSV
    $('#exportAllToCSV').on('click', function() {
        const data = validateExportData();
        if (!data) return;

        const csvContent = [exportConfig.headers.join(',')]
            .concat(data.map(row => row.map(cell => `"${cell || '-'}"`).join(',')))
            .join('\n');

        const blob = new Blob([csvContent], {
            type: 'text/csv'
        });
        saveAs(blob, exportConfig.fileName + '.csv');
    });

    // Print Semua Data
    $('#printAllData').on('click', function() {
        const data = validateExportData();
        if (!data) return;

        const printWindow = window.open('', '_blank');
        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Data Peminjaman Semua</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h2 { text-align: center; margin-bottom: 10px; }
                .info { text-align: center; margin-bottom: 20px; color: #666; }
                table { border-collapse: collapse; width: 100%; font-size: 12px; }
                th, td { border: 1px solid black; padding: 6px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <h2>Data Peminjaman Semua Anggota</h2>
            <div class="info">
                <p>Total Data: ${data.length} Peminjaman</p>
                <p>Tanggal Print: ${exportConfig.dateExport}</p>
            </div>
            <table>
                <thead>
                    <tr>${exportConfig.headers.map(h => `<th>${h}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${data.map(row => `<tr>${row.map(cell => `<td>${cell || '-'}</td>`).join('')}</tr>`).join('')}
                </tbody>
            </table>
        </body>
        </html>`;

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
}

/**
 * Fungsi untuk halaman index - Inisialisasi DataTable
 * Menangani: konfigurasi DataTable untuk semua tabel (Siswa, Guru, Staff)
 */
function initDataTables() {
    // Fungsi untuk ekspor data tabel ke format Word
    function exportToWord(dt) {
        // Ambil data tabel termasuk kolom catatan yang tersembunyi
        const data = dt.buttons.exportData({
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Semua kolom kecuali Aksi (indeks 9)
        });

        // Template HTML untuk dokumen Word dengan orientasi landscape
        const wordTemplate = `
        <!DOCTYPE html>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
        <head>
            <meta charset="utf-8">
            <title>Data Peminjaman Buku</title>
            <!--[if gte mso 9]>
            <xml><w:WordDocument><w:View>Print</w:View><w:Zoom>90</w:Zoom><w:Orientation>Landscape</w:Orientation></w:WordDocument></xml>
            <![endif]-->
            <style>
                @page { size: A4 landscape; margin: 0.5in; }
                body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; }
                .header { text-align: center; margin-bottom: 15px; }
                .header h2 { font-size: 14px; margin: 0 0 5px 0; }
                .date { text-align: center; margin-bottom: 15px; color: #666; font-size: 9px; }
                table { border-collapse: collapse; width: 100%; font-size: 8px; table-layout: fixed; }
                th, td { border: 1px solid #ddd; padding: 4px; text-align: left; word-wrap: break-word; vertical-align: top; }
                th { background-color: #f2f2f2; font-weight: bold; font-size: 9px; }
            </style>
        </head>
        <body>
            <div class="header"><h2>Data Peminjaman Buku</h2></div>
            <div class="date"><p>Data per tanggal ${new Date().toLocaleDateString('id-ID')}</p></div>
            <table><thead><tr>${data.header.map(h => `<th>${h}</th>`).join('')}</tr></thead>
            <tbody>${data.body.map(row => `<tr>${row.map(cell => `<td>${cell.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()}</td>`).join('')}</tr>`).join('')}</tbody>
            </table>
        </body>
        </html>`;

        const blob = new Blob([wordTemplate], {
            type: 'application/msword'
        });
        saveAs(blob, `Data_Peminjaman_Buku_${new Date().toLocaleDateString('id-ID').replace(/\//g, '-')}.doc`);
    }

    // Konfigurasi standar untuk DataTable
    const dataTableConfig = {
        responsive: true,
        order: [
            [0, 'asc']
        ],
        dom: '<"export-buttons-container"B>lfrtip',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
        },
        columnDefs: [{
                responsivePriority: 1,
                targets: [0, 1, 2, 3, 7]
            }, // Prioritas kolom utama
            {
                responsivePriority: 2,
                targets: [9]
            }, // Kolom aksi
            {
                orderable: false,
                targets: [-1]
            }, // Kolom aksi tidak dapat diurutkan
            {
                visible: false,
                targets: [8]
            } // Sembunyikan kolom catatan
        ]
    };

    // Fungsi untuk membuat tombol export DataTable
    const createExportButtons = (userType) => ([{
            extend: 'copy',
            text: '<i class="bx bx-copy"></i><span>Copy</span>',
            className: 'btn btn-outline-primary btn-sm export-btn',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            extend: 'csv',
            text: '<i class="bx bx-file"></i><span>CSV</span>',
            className: 'btn btn-outline-success btn-sm export-btn',
            filename: `Data_Peminjaman_${userType}_${new Date().toLocaleDateString('id-ID').replace(/\//g, '-')}`,
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            extend: 'excel',
            text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
            className: 'btn btn-outline-success btn-sm export-btn',
            filename: `Data_Peminjaman_${userType}_${new Date().toLocaleDateString('id-ID').replace(/\//g, '-')}`,
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            text: '<i class="bx bxs-file-doc"></i><span>Word</span>',
            className: 'btn btn-outline-info btn-sm export-btn',
            action: (e, dt) => exportToWord(dt)
        },
        {
            extend: 'pdf',
            text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
            className: 'btn btn-outline-danger btn-sm export-btn',
            filename: `Data_Peminjaman_${userType}_${new Date().toLocaleDateString('id-ID').replace(/\//g, '-')}`,
            orientation: 'landscape',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        },
        {
            extend: 'print',
            text: '<i class="bx bx-printer"></i><span>Print</span>',
            className: 'btn btn-outline-warning btn-sm export-btn',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
            }
        }
    ]);

    // Inisialisasi DataTable untuk semua tabel
    ['Siswa', 'Guru', 'Staff'].forEach(userType => {
        const tableElement = document.querySelector(`#table${userType}`);
        if (tableElement) {
            // Hancurkan DataTable yang sudah ada untuk menghindari duplikasi
            if ($.fn.DataTable.isDataTable(`#table${userType}`)) {
                $(`#table${userType}`).DataTable().destroy();
            }

            $(`#table${userType}`).DataTable({
                ...dataTableConfig,
                buttons: createExportButtons(userType)
            });
        }
    });
}

// ============================================================================
// FUNGSI UNTUK HALAMAN DETAIL (detail.blade.php)
// ============================================================================

/**
 * Fungsi untuk halaman detail peminjaman
 * Menangani: tombol pengambilan, konfirmasi pengembalian dengan modal sanksi
 */
function initPeminjamanDetail() {
    // Handler untuk tombol pengambilan
    document.querySelectorAll('.btn-success-pengambilan').forEach(button => {
        button.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            document.getElementById('pengambilan-form').setAttribute('action', actionUrl);
        });
    });

    // Handler untuk tombol konfirmasi pengembalian - menggunakan modal sanksi
    const btnKonfirmasi = document.getElementById('btnKonfirmasiPengembalian');
    if (btnKonfirmasi) {
        btnKonfirmasi.addEventListener('click', function() {
            // Gunakan data yang sudah disediakan oleh halaman detail
            if (typeof window.peminjamanDetailData !== 'undefined' && typeof window.showSanksiModal === 'function') {
                window.showSanksiModal(window.peminjamanDetailData);
            } else {
                console.error('Data peminjaman atau fungsi showSanksiModal tidak ditemukan');
            }
        });
    }
}

// ============================================================================
// FUNGSI UNTUK HALAMAN FORM (form.blade.php)
// ============================================================================

/**
 * Fungsi untuk halaman form peminjaman
 * Menangani: validasi tanggal, batasan tanggal kembali maksimal 3 hari
 */
function initPeminjamanForm() {
    // Batasi tanggal kembali maksimal 3 hari dari tanggal pinjam
    const tanggalPinjamInput = document.getElementById('tanggal_pinjam');
    const tanggalKembaliInput = document.getElementById('tanggal_kembali');

    if (tanggalPinjamInput && tanggalKembaliInput) {
        tanggalPinjamInput.addEventListener('change', function() {
            const tanggalPinjam = new Date(this.value);
            const tanggalMax = new Date(tanggalPinjam);
            tanggalMax.setDate(tanggalPinjam.getDate() + 3);

            const tanggalMin = new Date(tanggalPinjam);
            tanggalMin.setDate(tanggalPinjam.getDate() + 1);

            tanggalKembaliInput.min = tanggalMin.toISOString().split('T')[0];
            tanggalKembaliInput.max = tanggalMax.toISOString().split('T')[0];

            // Reset tanggal kembali jika tanggal yang dipilih tidak valid
            const currentKembali = new Date(tanggalKembaliInput.value);
            if (currentKembali < tanggalMin || currentKembali > tanggalMax) {
                tanggalKembaliInput.value = tanggalMin.toISOString().split('T')[0];
            }
        });

        // Trigger event pada saat halaman dimuat
        tanggalPinjamInput.dispatchEvent(new Event('change'));
    }
}

// ============================================================================
// FUNGSI UNTUK HALAMAN MANUAL (manual.blade.php)
// ============================================================================

/**
 * Fungsi untuk halaman form peminjaman manual
 * Menangani: pencarian buku, load anggota, validasi tanggal, update detail buku
 */
function initPeminjamanManual() {
    // Simpan opsi asli untuk pencarian buku
    var originalOptions = [];
    var bukuSelect = $('#buku_id');

    // Pastikan elemen ada sebelum digunakan
    if (bukuSelect.length === 0) {
        console.log('Element #buku_id tidak ditemukan');
        return;
    }

    // Simpan semua opsi buku ke array untuk dibaca dalam pencarian
    bukuSelect.find('option').each(function() {
        originalOptions.push({
            value: $(this).val(),
            text: $(this).text(),
            html: $(this)[0].outerHTML,
            judul: $(this).attr('data-judul') || '',
            pengarang: $(this).attr('data-pengarang') || ''
        });
    });

    // Fungsi pencarian buku
    $('#searchBuku').on('input keyup', function() {
        var kata = $(this).val().toLowerCase().trim();

        if (kata === '') {
            // Tampilkan semua opsi
            bukuSelect.find('option').remove();
            originalOptions.forEach(function(option) {
                bukuSelect.append(option.html);
            });
        } else {
            // Filter berdasarkan kata kunci
            bukuSelect.find('option').remove();
            bukuSelect.append('<option value="">-- Pilih Buku --</option>');

            var hasilFilter = originalOptions.filter(function(option) {
                if (option.value === '') return false;
                return option.judul.toLowerCase().includes(kata) ||
                    option.pengarang.toLowerCase().includes(kata) ||
                    option.text.toLowerCase().includes(kata);
            });

            hasilFilter.forEach(function(option) {
                bukuSelect.append(option.html);
            });

            // Tampilkan pesan jika tidak ada hasil
            if (hasilFilter.length === 0) {
                bukuSelect.append(
                    '<option value="" disabled>Tidak ada buku yang ditemukan</option>');
            }
        }

        // Reset pilihan jika tidak ada di hasil filter
        if (bukuSelect.val() && bukuSelect.find('option:selected').length === 0) {
            bukuSelect.val('').trigger('change');
        }
    });

    // Auto-pilih jika hanya 1 hasil saat Enter
    $('#searchBuku').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var opsiTerlihat = bukuSelect.find('option:not(:first):not(:disabled)');
            if (opsiTerlihat.length === 1) {
                bukuSelect.val(opsiTerlihat.first().val()).trigger('change');
                $(this).blur();
            }
        }
    });

    // Update field pencarian saat buku dipilih
    $('#buku_id').on('change', function() {
        if ($(this).val()) {
            var judulTerpilih = $(this).find('option:selected').attr('data-judul');
            if (judulTerpilih) {
                $('#searchBuku').val(judulTerpilih);
            }
        } else {
            $('#searchBuku').val('');
        }
    });

    // Tombol hapus pencarian
    $('#clearSearch').on('click', function() {
        $('#searchBuku').val('').trigger('input');
        $('#buku_id').val('').trigger('change'); // Reset pilihan buku dan detail
        $('#searchBuku').focus();
    });

    // Tampilkan detail buku saat dipilih
    $('#buku_id').on('change', function() {
        var bukuId = $(this).val();
        var selectedOption = $(this).find('option:selected');

        if (bukuId && bukuId !== '') {
            // Ambil data buku
            var judul = selectedOption.attr('data-judul');
            var pengarang = selectedOption.attr('data-pengarang');
            var penerbit = selectedOption.attr('data-penerbit');
            var stok = selectedOption.attr('data-stok');
            var kategori = selectedOption.attr('data-kategori');
            var foto = selectedOption.attr('data-foto');
            var kodeBuku = selectedOption.attr('data-kode-buku');
            var tahunTerbit = selectedOption.attr('data-tahun-terbit');
            var status = selectedOption.attr('data-status');

            // Update gambar buku - gunakan base URL dinamis
            var baseUrl = window.location.origin;
            if (foto) {
                $('#bukuImage').attr('src', baseUrl + '/assets/img/buku/' + foto)
                    .attr('alt', judul).show();
                $('#defaultImage').hide();
            } else {
                $('#bukuImage').hide();
                $('#defaultImage').show();
            }

            // Tentukan warna status
            var warnaStatus = 'orange';
            if (status === 'Tersedia') warnaStatus = 'green';
            else if (status === 'Habis') warnaStatus = 'red';

            // Update info buku
            var infoBuku = `
                <h5 class="card-title text-center">${judul || 'N/A'}</h5>
                <p class="card-text m-0" style="text-align: justify;">Kode Buku: ${kodeBuku || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Pengarang: ${pengarang || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Kategori: ${kategori || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Penerbit: ${penerbit || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Tahun Terbit: ${tahunTerbit || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Stok: ${stok || 'N/A'}</p>
                <p class="card-text m-0" style="text-align: justify;">Status:
                    <span style="color: ${warnaStatus};">${status || 'N/A'}</span>
                </p>
            `;
            $('#bukuInfo').html(infoBuku);
        } else {
            // Reset ke keadaan awal
            $('#bukuImage').hide();
            $('#defaultImage').show();
            $('#bukuInfo').html(`
                <h5 class="card-title text-center text-muted">
                    Pilih buku untuk melihat detail
                </h5>
            `);
        }
    });

    // Load anggota berdasarkan level
    $('#user_level').change(function() {
        var level = $(this).val();
        var userSelect = $('#user_id');

        if (level) {
            $('#loadingOverlay').show();
            userSelect.prop('disabled', true).html('<option value="">Loading...</option>');

            // Gunakan base URL dinamis
            var baseUrl = window.location.origin;
            $.ajax({
                url: baseUrl + "/peminjaman/manual/get-anggota/" + level,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    userSelect.html('<option value="">-- Pilih Anggota --</option>');

                    if (data.length > 0) {
                        $.each(data, function(index, anggota) {
                            // Gunakan nilai dari window.oldUserId yang sudah di-set oleh halaman
                            var selected = (typeof window.oldUserId !== 'undefined' && window.oldUserId == anggota.id) ? 'selected' : '';
                            userSelect.append('<option value="' + anggota.id +
                                '" ' + selected + '>' +
                                anggota.nama + ' (' + anggota.info +
                                ')</option>');
                        });
                        userSelect.prop('disabled', false);
                    } else {
                        userSelect.html(
                            '<option value="">Tidak ada anggota tersedia untuk level ' +
                            level + '</option>');
                    }
                    $('#loadingOverlay').hide();
                },
                error: function() {
                    userSelect.html('<option value="">Error memuat data</option>');
                    $('#loadingOverlay').hide();
                    alert('Terjadi kesalahan saat memuat data anggota');
                }
            });
        } else {
            userSelect.html('<option value="">-- Pilih level terlebih dahulu --</option>');
            userSelect.prop('disabled', true);
        }
    });

    // Auto-set tanggal kembali (maksimal 3 hari)
    $('#tanggal_pinjam').change(function() {
        const tanggalPinjam = new Date($(this).val());
        if (tanggalPinjam) {
            const tanggalKembali = new Date(tanggalPinjam);
            tanggalKembali.setDate(tanggalPinjam.getDate() + 1);

            const tanggalMax = new Date(tanggalPinjam);
            tanggalMax.setDate(tanggalPinjam.getDate() + 3);

            const inputKembali = $('#tanggal_kembali');
            inputKembali.attr('min', tanggalKembali.toISOString().split('T')[0]);
            inputKembali.attr('max', tanggalMax.toISOString().split('T')[0]);
            inputKembali.val(tanggalKembali.toISOString().split('T')[0]);
        }
    });

    // Validasi tanggal kembali
    $('#tanggal_kembali').change(function() {
        var tanggalPinjam = new Date($('#tanggal_pinjam').val());
        var tanggalKembali = new Date($(this).val());
        var tanggalMax = new Date(tanggalPinjam);
        tanggalMax.setDate(tanggalPinjam.getDate() + 3);

        if (tanggalKembali <= tanggalPinjam) {
            alert('Tanggal kembali harus setelah tanggal pinjam');
            $(this).val('');
        } else if (tanggalKembali > tanggalMax) {
            alert('Tanggal kembali maksimal 3 hari dari tanggal pinjam');
            $(this).val('');
        }
    });

    // Trigger event untuk data lama
    if ($('#user_level').val()) $('#user_level').trigger('change');
    if ($('#buku_id').val()) $('#buku_id').trigger('change');
    $('#tanggal_pinjam').trigger('change');

    // Validasi form sebelum submit
    $('#manualPeminjamanForm').submit(function(e) {
        $('#submitBtn').prop('disabled', true).html(
            '<i class="bx bx-loader bx-spin"></i> Menyimpan...');
    });
}

// ============================================================================

/**
 * Inisialisasi otomatis saat DOM loaded
 * Mendeteksi halaman dan menjalankan fungsi yang sesuai
 */
$(document).ready(function() {
    // Deteksi halaman berdasarkan URL
    const currentPath = window.location.pathname;

    // Variabel untuk mencegah duplikasi inisialisasi
    let isInitialized = false;

    if (currentPath.includes('/peminjaman')) {
        if (currentPath.includes('/detail')) {
            // Halaman detail peminjaman
            initPeminjamanDetail();
            isInitialized = true;
        } else if (currentPath.includes('/form') || currentPath.includes('/create')) {
            // Halaman form peminjaman
            initPeminjamanForm();
            isInitialized = true;
        } else if (currentPath.includes('/manual')) {
            // Halaman form peminjaman manual
            initPeminjamanManual();
            isInitialized = true;
        } else {
            // Halaman index peminjaman
            initPeminjamanIndex();
            isInitialized = true;
        }
    }

    // Fallback: Deteksi berdasarkan elemen yang ada di halaman (hanya jika belum diinisialisasi)
    if (!isInitialized) {
        if (document.querySelector('#tableSiswa') || document.querySelector('#tableGuru') || document.querySelector('#tableStaff')) {
            // Halaman index dengan DataTable
            initPeminjamanIndex();
        } else if (document.querySelector('#buku_id') && document.querySelector('#user_level')) {
            // Halaman manual peminjaman
            initPeminjamanManual();
        } else if (document.querySelector('#tanggal_pinjam') && document.querySelector('#tanggal_kembali') && !document.querySelector('#user_level')) {
            // Halaman form peminjaman
            initPeminjamanForm();
        } else if (document.querySelector('#btnKonfirmasiPengembalian')) {
            // Halaman detail peminjaman
            initPeminjamanDetail();
        }
    }
});
