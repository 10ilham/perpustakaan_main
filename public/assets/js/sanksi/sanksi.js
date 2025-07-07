$(document).ready(function() {
    // Konfigurasi DataTable berdasarkan level user
    var isAdmin = typeof window.userLevel !== 'undefined' && window.userLevel === 'admin';

    // DataTable setup
    var tableConfig = {
        responsive: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(difilter dari _MAX_ total entri)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        order: [
            isAdmin ? [4, "desc"] : [3, "desc"] // Tanggal Sanksi
        ],
        columnDefs: [{
            orderable: false,
            targets: [isAdmin ? 10 : -1]
        }]
    };

    // Tambahkan konfigurasi export buttons untuk admin
    if (isAdmin) {
        tableConfig.dom = '<"export-buttons-container"B>lfrtip';
        tableConfig.buttons = [
            {
                extend: 'copy',
                text: '<i class="bx bx-copy"></i><span>Copy</span>',
                className: 'btn btn-outline-primary btn-sm export-btn',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude action column
                }
            },
            {
                extend: 'csv',
                text: '<i class="bx bx-file"></i><span>CSV</span>',
                className: 'btn btn-outline-success btn-sm export-btn',
                filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude action column
                }
            },
            {
                extend: 'excel',
                text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                className: 'btn btn-outline-success btn-sm export-btn',
                filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                title: 'Data Sanksi & Denda',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude action column
                }
            },
            {
                text: '<i class="bx bxs-file-doc"></i><span>Word</span>',
                className: 'btn btn-outline-info btn-sm export-btn',
                action: function(e, dt, button, config) {
                    // Custom Word export function
                    var data = dt.buttons.exportData({
                        columns: ':not(:last-child)'
                    });

                    var wordTemplate = `
                    <!DOCTYPE html>
                    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
                    <head>
                        <meta charset="utf-8">
                        <title>Data Sanksi & Denda</title>
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
                        <div class="header"><h2>Data Sanksi & Denda</h2></div>
                        <div class="date"><p>Data per tanggal ${new Date().toLocaleDateString('id-ID')}</p></div>
                        <table><thead><tr>${data.header.map(h => `<th>${h}</th>`).join('')}</tr></thead>
                        <tbody>${data.body.map(row => `<tr>${row.map(cell => `<td>${cell.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()}</td>`).join('')}</tr>`).join('')}</tbody>
                        </table>
                    </body>
                    </html>`;

                    var blob = new Blob([wordTemplate], {
                        type: 'application/msword'
                    });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-') + '.doc';
                    a.click();
                    URL.revokeObjectURL(url);
                }
            },
            {
                extend: 'pdf',
                text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
                className: 'btn btn-outline-danger btn-sm export-btn',
                filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-'),
                title: 'Data Sanksi & Denda',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude action column
                }
            },
            {
                extend: 'print',
                text: '<i class="bx bx-printer"></i><span>Print</span>',
                className: 'btn btn-outline-warning btn-sm export-btn',
                title: 'Data Sanksi & Denda',
                exportOptions: {
                    columns: ':not(:last-child)' // Exclude action column
                }
            }
        ];
    }

    var table = $('#sanksiTable').DataTable(tableConfig);

    // Cek filter aktif
    function hasFilter() {
        var hasActive = false;
        if (isAdmin && $('#filterAnggota').val() !== '') hasActive = true;
        if ($('#filterSanksi').val() !== '') hasActive = true;
        if ($('#filterStatus').val() !== '') hasActive = true;
        return hasActive;
    }

    // Update card total
    function updateCards() {
        var rows = table.rows({
            search: 'applied'
        }).data();
        var total = 0, belum = 0, sudah = 0;

        rows.each(function(row) {
            var dendaIndex = isAdmin ? 9 : 8; // Total Denda
            var statusIndex = isAdmin ? 10 : 9; // Status Bayar

            var denda = parseInt($(row[dendaIndex]).text().replace(/[^\d]/g, '')) || 0;
            var status = $(row[statusIndex]).text();
            total += denda;
            if (status.includes('Belum')) belum += denda;
            if (status.includes('Sudah')) sudah += denda;
        });

        $('#totalKeseluruhan').text('Rp ' + total.toLocaleString('id-ID'));
        $('#totalBelumBayar').text('Rp ' + belum.toLocaleString('id-ID'));
        $('#totalSudahBayar').text('Rp ' + sudah.toLocaleString('id-ID'));

        // Atur card layout
        var filter = $('#filterStatus').val();
        var $cards = $('#cardTotalDenda, #cardBelumBayar, #cardSudahBayar');
        $cards.show().removeClass('col-md-6').addClass('col-md-4');

        if (filter === 'belum_bayar') {
            $('#cardSudahBayar').hide();
            $('#cardTotalDenda, #cardBelumBayar').removeClass('col-md-4').addClass('col-md-6');
        } else if (filter === 'sudah_bayar') {
            $('#cardBelumBayar').hide();
            $('#cardTotalDenda, #cardSudahBayar').removeClass('col-md-4').addClass('col-md-6');
        }
    }

    // Apply filter
    window.applyFilters = function() {
        if (isAdmin) {
            table.column(1).search($('#filterAnggota').val());
        }

        var jenisSanksiIndex = isAdmin ? 5 : 4; // Jenis Sanksi
        var statusBayarIndex = isAdmin ? 10 : 9; // Status Bayar

        var sanksi = $('#filterSanksi').val();
        var status = $('#filterStatus').val();

        table.column(jenisSanksiIndex).search(
            sanksi === 'keterlambatan' ? 'Keterlambatan' :
            sanksi === 'rusak_hilang' ? 'Rusak/Hilang' : ''
        );
        table.column(statusBayarIndex).search(
            status === 'belum_bayar' ? 'Belum Bayar' :
            status === 'sudah_bayar' ? 'Sudah Bayar' : ''
        );

        table.draw();
        setTimeout(updateCards, 50);

        // Tampilkan tombol reset jika ada filter aktif
        if (hasFilter()) {
            $('#resetBtn').show();
        } else {
            $('#resetBtn').hide();
        }
    };

    // Reset filter
    window.resetFilters = function() {
        if (isAdmin) {
            $('#filterAnggota').val('');
        }
        $('#filterSanksi, #filterStatus').val('');
        table.search('').columns().search('').draw();
        setTimeout(updateCards, 50);
        $('#resetBtn').hide();
    };

    // Modal pembayaran
    window.showPaymentModal = function(id) {
        if (typeof window.sanksiPaymentRoute !== 'undefined') {
            $('#paymentForm').attr('action', window.sanksiPaymentRoute.replace(':id', id));
            $('#paymentModal').modal('show');
        }
    };
});
