// File JavaScript untuk halaman kategori
$(document).ready(function() {
    // Delay untuk menghindari konflik dengan DataTable global
    setTimeout(function() {
        // Cek apakah user adalah admin
        var isAdmin = typeof window.userLevel !== 'undefined' && window.userLevel === 'admin';

        // Hapus DataTable yang sudah ada jika ada
        if ($.fn.DataTable.isDataTable('#dataTableKategori')) {
            $('#dataTableKategori').DataTable().destroy();
        }

        // Konfigurasi dasar DataTable
        var config = {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
            },
            pageLength: 10,
            order: [[0, "asc"]], // Urutkan berdasarkan id kategori
            columnDefs: [{
                orderable: false,
                targets: isAdmin ? [4] : [3] // Kolom aksi tidak bisa diurutkan (sesuaikan dengan level user)
            }]
        };

        // Tambahkan tombol export hanya untuk admin
        if (isAdmin) {
            config.dom = '<"export-buttons-container"B>lfrtip';
            config.buttons = [
                {
                    extend: 'copy',
                    text: '<i class="bx bx-copy"></i><span>Copy</span>',
                    className: 'btn btn-outline-primary btn-sm export-btn',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'csv',
                    text: '<i class="bx bx-file"></i><span>CSV</span>',
                    className: 'btn btn-outline-success btn-sm export-btn',
                    filename: 'Data_Kategori_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'excel',
                    text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                    className: 'btn btn-outline-success btn-sm export-btn',
                    filename: 'Data_Kategori_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                    title: 'Data Kategori Buku',
                    exportOptions: { columns: ':visible' }
                },
                {
                    text: '<i class="bx bxs-file-doc"></i><span>Word</span>',
                    className: 'btn btn-outline-info btn-sm export-btn',
                    action: function(e, dt, button, config) {
                        // Export Word sederhana
                        var data = dt.buttons.exportData({
                            columns: ':visible'
                        });

                        var wordContent = `
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <title>Data Kategori Buku</title>
                        </head>
                        <body>
                            <h2 style="text-align: center;">Data Kategori Buku</h2>
                            <p style="text-align: center;">Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                            <table border="1" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>${data.header.map(h => `<th style="padding: 8px;">${h}</th>`).join('')}</tr>
                                </thead>
                                <tbody>
                                    ${data.body.map(row => `<tr>${row.map(cell => `<td style="padding: 8px;">${cell}</td>`).join('')}</tr>`).join('')}
                                </tbody>
                            </table>
                        </body>
                        </html>`;

                        var blob = new Blob([wordContent], { type: 'application/msword' });
                        var link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'Data_Kategori_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-') + '.doc';
                        link.click();
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
                    className: 'btn btn-outline-danger btn-sm export-btn',
                    filename: 'Data_Kategori_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                    title: 'Data Kategori Buku',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'print',
                    text: '<i class="bx bx-printer"></i><span>Print</span>',
                    className: 'btn btn-outline-warning btn-sm export-btn',
                    title: 'Data Kategori Buku',
                    exportOptions: { columns: ':visible' }
                }
            ];
        }

        // Inisialisasi DataTable dengan konfigurasi
        $('#dataTableKategori').DataTable(config);

        // Handler untuk tombol hapus
        $('.delete-btn').on('click', function() {
            var action = $(this).data('action');
            $('#delete-form').attr('action', action);
        });

    }, 300); // Delay 300ms
});
