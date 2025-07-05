@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Sanksi & Denda</div>
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'siswa')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'guru')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'staff')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @endif
            <li class="divider">/</li>
            <li><a class="active">Sanksi</a></li>
        </ul>

        <br>
        <!-- Area Filter untuk Sanksi-->

        <div class="filter">
            <div class="card">
                <div class="head">
                    <h3>Filter Sanksi</h3>
                </div>
                <div class="form-group" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                    {{-- Filter Anggota - Hanya untuk Admin --}}
                    @if (auth()->user()->level == 'admin')
                        <select id="filterAnggota" class="form-control" style="max-width: 180px; height: 40px;">
                            <option value="">Semua Anggota</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="staff">Staff</option>
                        </select>
                    @endif

                    <select id="filterSanksi" class="form-control" style="max-width: 180px; height: 40px;">
                        <option value="">Semua Sanksi</option>
                        <option value="keterlambatan">Keterlambatan</option>
                        <option value="rusak_hilang">Rusak/Hilang</option>
                    </select>

                    <select id="filterStatus" class="form-control" style="max-width: 180px; height: 40px;">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sudah_bayar">Sudah Bayar</option>
                    </select>

                    <button type="submit" class="btn btn-primary" style="padding: 5px 15px;" onclick="applyFilters()">
                        <i class='bx bx-filter'></i> Filter
                    </button>

                    <button id="resetBtn" class="btn btn-secondary" style="padding: 5px 15px; display: none;"
                        onclick="resetFilters()">
                        <i class='bx bx-reset'></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Sanksi</h3>
                </div>
                <div class="table-responsive">
                    <table id="sanksiTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                @if (auth()->user()->level == 'admin')
                                    <th>Level</th>
                                @endif
                                <th>Buku</th>
                                <th>Tanggal Sanksi</th>
                                <th>Jenis Sanksi</th>
                                <th>Hari Terlambat</th>
                                <th>Denda Keterlambatan</th>
                                <th>Denda Kerusakan</th>
                                <th>Total Denda</th>
                                <th>Status Bayar</th>
                                @if (auth()->user()->level == 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sanksi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->peminjaman->user->nama ?? $item->peminjaman->user->name }}</strong><br>
                                    </td>
                                    @if (auth()->user()->level == 'admin')
                                    <td>
                                        <strong>{{ ucfirst($item->peminjaman->user->level) }}</strong>
                                    </td>
                                    @endif
                                    <td>
                                        <strong>{{ $item->peminjaman->buku->judul }}</strong><br>
                                        {{-- <small class="text-muted">{{ $item->peminjaman->buku->kode_buku }}</small> --}}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $jenisSanksi = explode(',', $item->jenis_sanksi);
                                        @endphp
                                        @foreach ($jenisSanksi as $jenis)
                                            <span
                                                class="badge
                                                @if ($jenis == 'keterlambatan') badge-warning
                                                @elseif($jenis == 'rusak_hilang') badge-danger
                                                @else badge-dark @endif
                                            ">
                                                @if ($jenis == 'keterlambatan')
                                                    Keterlambatan
                                                @elseif($jenis == 'rusak_hilang')
                                                    Rusak/Hilang
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $jenis)) }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($item->hari_terlambat > 0)
                                            <span class="badge badge-warning">{{ $item->hari_terlambat }} hari</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->denda_keterlambatan > 0)
                                            <span class="text-warning">Rp
                                                {{ number_format($item->denda_keterlambatan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->denda_kerusakan > 0)
                                            <span class="text-danger">Rp
                                                {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-primary">Rp
                                            {{ number_format($item->total_denda, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if ($item->status_bayar == 'sudah_bayar')
                                            <span class="badge badge-success">Sudah Bayar</span>
                                        @else
                                            <span class="badge badge-danger">Belum Bayar</span>
                                        @endif
                                    </td>

                                    @if (auth()->user()->level == 'admin')
                                        <td>
                                            @if ($item->status_bayar == 'belum_bayar')
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="showPaymentModal('{{ $item->id }}')">
                                                    <i class="bx bx-check"></i> Konfirmasi Bayar
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->level == 'admin' ? '11' : '10' }}" class="text-center">
                                        <div class="empty-state">
                                            <i class="bx bx-info-circle"></i>
                                            <h3>Tidak ada data sanksi</h3>
                                            <p>Belum ada sanksi atau denda yang tercatat</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($sanksi->count() > 0)
                    <div class="mt-3">
                        <div class="row" id="dendaCards">
                            <div class="col-md-4" id="cardBelumBayar">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Belum Dibayar</h6>
                                        <h4 class="text-danger" id="totalBelumBayar">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'belum_bayar')->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="cardSudahBayar">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Sudah Dibayar</h6>
                                        <h4 class="text-success" id="totalSudahBayar">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'sudah_bayar')->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="cardTotalDenda">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Keseluruhan Denda</h6>
                                        <h4 class="text-primary" id="totalKeseluruhan">
                                            Rp {{ number_format($sanksi->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Konfirmasi Pembayaran -->
        <div class="modal fade bootstrap-modal" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Konfirmasi Pembayaran Denda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Pastikan pembayaran denda telah diterima sebelum mengkonfirmasi.</p>
                        <p>Apakah Anda yakin ingin mengkonfirmasi pembayaran denda ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="paymentForm" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <!-- DataTables Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTable setup
            var table = $('#sanksiTable').DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ data)",
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
                    @if (auth()->user()->level == 'admin')
                        [4, "asc"] // Tanggal Sanksi di kolom ke-5 (index 4) untuk admin
                    @else
                        [3, "asc"] // Tanggal Sanksi di kolom ke-4 (index 3) untuk non-admin
                    @endif
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [
                        @if (auth()->user()->level == 'admin')
                            10
                        @else
                            -1
                        @endif
                    ]
                }],
                @if (auth()->user()->level == 'admin')
                    dom: '<"export-buttons-container"B>lfrtip',
                    buttons: [{
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
                            filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID')
                                .replace(/\//g, '-'),
                            exportOptions: {
                                columns: ':not(:last-child)' // Exclude action column
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                            className: 'btn btn-outline-success btn-sm export-btn',
                            filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID')
                                .replace(/\//g, '-'),
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
                                a.download = 'Data_Sanksi_Denda_' + new Date().toLocaleDateString(
                                    'id-ID').replace(/\//g, '-') + '.doc';
                                a.click();
                                URL.revokeObjectURL(url);
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
                            className: 'btn btn-outline-danger btn-sm export-btn',
                            filename: 'Data_Sanksi_Denda_' + new Date().toLocaleDateString('id-ID')
                                .replace(/\//g, '-'),
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
                    ]
                @endif
            });

            // Cek filter aktif
            function hasFilter() {
                var hasActive = false;
                @if (auth()->user()->level == 'admin')
                    if ($('#filterAnggota').val() !== '') hasActive = true;
                @endif
                if ($('#filterSanksi').val() !== '') hasActive = true;
                if ($('#filterStatus').val() !== '') hasActive = true;
                return hasActive;
            }

            // Update card total
            function updateCards() {
                var rows = table.rows({
                    search: 'applied'
                }).data();
                var total = 0,
                    belum = 0,
                    sudah = 0;

                rows.each(function(row) {
                    @if (auth()->user()->level == 'admin')
                        var dendaIndex = 9; // Total Denda di kolom ke-10 (index 9) untuk admin
                        var statusIndex = 10; // Status Bayar di kolom ke-11 (index 10) untuk admin
                    @else
                        var dendaIndex = 8; // Total Denda di kolom ke-9 (index 8) untuk non-admin
                        var statusIndex = 9; // Status Bayar di kolom ke-10 (index 9) untuk non-admin
                    @endif

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
                @if (auth()->user()->level == 'admin')
                    table.column(1).search($('#filterAnggota').val());
                    var jenisSanksiIndex = 5; // Jenis Sanksi di kolom ke-6 (index 5) untuk admin
                    var statusBayarIndex = 10; // Status Bayar di kolom ke-11 (index 10) untuk admin
                @else
                    var jenisSanksiIndex = 4; // Jenis Sanksi di kolom ke-5 (index 4) untuk non-admin
                    var statusBayarIndex = 9; // Status Bayar di kolom ke-10 (index 9) untuk non-admin
                @endif

                var sanksi = $('#filterSanksi').val();
                var status = $('#filterStatus').val();

                table.column(jenisSanksiIndex).search(sanksi === 'keterlambatan' ? 'Keterlambatan' : sanksi ===
                    'rusak_hilang' ? 'Rusak/Hilang' : '');
                table.column(statusBayarIndex).search(status === 'belum_bayar' ? 'Belum Bayar' : status === 'sudah_bayar' ?
                    'Sudah Bayar' : '');

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
                @if (auth()->user()->level == 'admin')
                    $('#filterAnggota').val('');
                @endif
                $('#filterSanksi, #filterStatus').val('');
                table.search('').columns().search('').draw();
                setTimeout(updateCards, 50);
                $('#resetBtn').hide();
            };

            // Modal pembayaran
            window.showPaymentModal = function(id) {
                $('#paymentForm').attr('action', '{{ route('sanksi.bayar', ':id') }}'.replace(':id', id));
                $('#paymentModal').modal('show');
            };
        });
    </script>
@endsection

@section('styles')
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        /* Tampilan untuk state kosong */
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            margin-bottom: 8px;
            color: #495057;
        }

        .empty-state p {
            margin-bottom: 0;
            font-size: 14px;
        }

        /* Styling untuk badge */
        .badge {
            font-size: 0.75em;
            padding: 0.25em 0.5em;
            border-radius: 0.25rem;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-dark {
            background-color: #343a40;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        /* Styling untuk area filter */
        .filter {
            margin-bottom: 20px;
        }

        .filter .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .filter .head h3 {
            color: #333;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Styling untuk tombol filter */
        .filter .form-group .btn {
            height: 38px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .filter .form-group .btn-primary {
            background: #3c91e6;
            border: 1px solid #3c91e6;
            color: white;
        }

        .filter .form-group .btn-primary:hover {
            background: #2980d1;
            border-color: #2980d1;
            transform: translateY(-1px);
        }

        .filter .form-group .btn-secondary {
            background: #6c757d;
            border: 1px solid #6c757d;
            color: white;
        }

        .filter .form-group .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
            transform: translateY(-1px);
        }

        /* Styling untuk form control */
        .filter .form-control {
            height: 38px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .filter .form-control:focus {
            border-color: #3c91e6;
            box-shadow: 0 0 0 0.2rem rgba(60, 145, 230, 0.25);
        }

        /* Styling DataTable */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: white !important;
        }

        /* Styling Modal Pembayaran */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            color: #333;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        .modal-footer .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        /* Styling untuk DataTables Buttons */
        .export-buttons-container {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dt-buttons {
            margin-bottom: 15px;
        }

        .export-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 90px;
            justify-content: center;
        }

        .export-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .export-btn i {
            font-size: 16px;
        }

        .export-btn span {
            font-weight: 500;
        }

        /* Custom colors for export buttons */
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .btn-outline-success {
            color: #28a745;
            border-color: #28a745;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .btn-outline-info {
            color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-outline-warning {
            color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
    </style>
@endsection
