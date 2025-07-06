// Javascript untuk halaman index anggota (untuk export)

$(document).ready(function() {
    // Get current date in dd-mm-yyyy format
    const currentDate = new Date().toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).replace(/\//g, '-');

    // Initialize DataTable with export buttons
    $('#dataTableExport').DataTable({
        responsive: true,
        order: [
            [0, 'asc']
        ], // Sort by the first column (No) in ascending order
        dom: '<"export-buttons-container"B>lfrtip',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
        },
        buttons: [{
                extend: 'copy',
                text: '<i class="bx bx-copy"></i><span>Copy</span>',
                className: 'btn btn-outline-primary btn-sm export-btn',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                }
            },
            {
                extend: 'csv',
                text: '<i class="bx bx-file"></i><span>CSV</span>',
                className: 'btn btn-outline-success btn-sm export-btn',
                filename: 'Data_Anggota_' + currentDate,
                exportOptions: {
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                }
            },
            {
                extend: 'excel',
                text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                className: 'btn btn-outline-success btn-sm export-btn',
                filename: 'Data_Anggota_' + currentDate,
                exportOptions: {
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                }
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
                filename: 'Data_Anggota_' + currentDate,
                orientation: 'landscape',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                }
            },
            {
                extend: 'print',
                text: '<i class="bx bx-printer"></i><span>Print</span>',
                className: 'btn btn-outline-warning btn-sm export-btn',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                }
            }
        ],
        columnDefs: [{
                responsivePriority: 1,
                targets: [0, 2, 4]
            }, // No, Nama, Level
            {
                responsivePriority: 2,
                targets: [6]
            }, // Tombol Aksi
            {
                orderable: false,
                targets: [1, 6]
            }, // Foto dan Tombol Aksi tidak dapat diurutkan
            {
                searchable: false,
                targets: [1, 6]
            } // Foto dan Tombol Aksi tidak dapat dicari
        ]
    });

    // Function to export table data to Word format
    function exportToWord(dt) {
        // Get current date for filename and display
        const currentDate = new Date().toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        }).replace(/\//g, '-');

        const displayDate = new Date().toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        // Get table data
        const data = dt.buttons.exportData({
            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
        });

        // Create HTML content for Word document
        let htmlContent = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office"
                    xmlns:w="urn:schemas-microsoft-com:office:word"
                    xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <title>Data Anggota Perpustakaan</title>
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
                    @page {
                        size: A4 landscape;
                        margin: 0.5in;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 10px;
                        margin: 0;
                        padding: 0;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 15px;
                    }
                    .header h2 {
                        font-size: 14px;
                        margin: 0 0 5px 0;
                    }
                    .date {
                        text-align: center;
                        margin-bottom: 15px;
                        color: #666;
                        font-size: 9px;
                    }
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-size: 9px;
                        table-layout: fixed;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 6px;
                        text-align: left;
                        word-wrap: break-word;
                        overflow-wrap: break-word;
                        vertical-align: top;
                    }
                    th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                        font-size: 10px;
                    }
                    /* Column widths */
                    .col-no { width: 8%; }
                    .col-nama { width: 30%; }
                    .col-email { width: 35%; }
                    .col-level { width: 15%; }
                    .col-nip-nisn { width: 12%; }
                    .text-center { text-align: center; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>Data Anggota Perpustakaan</h2>
                </div>
                <div class="date">
                    <p>Data per tanggal ${displayDate}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-nama">Nama</th>
                            <th class="col-email">Email</th>
                            <th class="col-level">Level</th>
                            <th class="col-nip-nisn">NIP/NISN</th>
                        </tr>
                    </thead>
                    <tbody>`;

        // Add data rows
        data.body.forEach(function(row) {
            htmlContent += '<tr>';
            row.forEach(function(cell, index) {
                // Clean cell data (remove HTML tags and extra spaces)
                let cleanCell = cell.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
                const columnClasses = ['col-no', 'col-nama', 'col-email', 'col-level',
                    'col-nip-nisn'
                ];
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

        // Create blob and download
        const blob = new Blob([htmlContent], {
            type: 'application/msword'
        });

        const fileName = 'Data_Anggota_' + currentDate + '.doc';

        // Use FileSaver.js to download the file
        saveAs(blob, fileName);
    }

    // Modal delete functionality
    $('.delete-btn').on('click', function() {
        var action = $(this).data('action');
        $('#delete-form').attr('action', action);
    });
});
// End untuk javascript index anggota

// Untuk halaman tambah anggota
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    const levelFields = document.querySelectorAll('.level-fields');

    // Inisialisasi form - tampilkan field berdasarkan level yang dipilih (jika ada)
    showFieldsBasedOnLevel(levelSelect.value);

    // Event listener untuk perubahan level
    levelSelect.addEventListener('change', function() {
        showFieldsBasedOnLevel(this.value);
    });

    // Fungsi untuk menampilkan field berdasarkan level yang dipilih
    function showFieldsBasedOnLevel(level) {
        // Sembunyikan semua field khusus dulu
        levelFields.forEach(field => {
            field.style.display = 'none';

            // Nonaktifkan input di dalam field yang disembunyikan
            const inputs = field.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = true;
            });
        });

        // Tampilkan field sesuai level yang dipilih
        if (level) {
            const selectedField = document.getElementById(level + '-fields');
            if (selectedField) {
                selectedField.style.display = 'block';

                // Aktifkan input di dalam field yang ditampilkan
                const inputs = selectedField.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                });
            }
        }
    }

    // Jika ada nilai level yang sudah dipilih sebelumnya (misalnya karena validation error)
    if ("{{ old('level') }}") {
        showFieldsBasedOnLevel("{{ old('level') }}");
    }
});
// End untuk halaman tambah anggota
