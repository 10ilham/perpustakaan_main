@extends('layouts.app')

@section('content')
    <main>
        @if (auth()->user()->level == 'admin')
            <h1 class="title">Laporan Sanksi Belum Bayar</h1>
        @else
            <h1 class="title">Sanksi Belum Bayar</h1>
        @endif
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="divider">/</li>
                <li><a href="{{ route('laporan.sanksi') }}">Laporan Sanksi</a></li>
                <li class="divider">/</li>
                <li><a class="active">Belum Bayar</a></li>
            @else
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                <li class="divider">/</li>
                <li><a class="active">Sanksi Belum Bayar</a></li>
            @endif
        </ul>

        {{-- Button untuk kembali ke dashboard laporan (hanya untuk admin) --}}
        @if (auth()->user()->level == 'admin')
            <div class="back-button">
                <a href="{{ route('laporan.sanksi') }}" class="btn-secondary-laporan">
                    <i class='bx bx-arrow-back'></i> Kembali
                </a>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Filter Laporan</h3>
                </div>
                <form method="GET" action="{{ route('laporan.sanksi.belum_bayar') }}" class="filter-form-grid">
                    <div class="filter-form-group">
                        <label for="tanggal_mulai" class="filter-form-label">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="filter-form-input">
                    </div>
                    <div class="filter-form-group">
                        <label for="tanggal_akhir" class="filter-form-label">Tanggal Akhir</label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                            class="filter-form-input">
                    </div>
                    @if (auth()->user()->level == 'admin')
                        <div class="filter-form-group">
                            <label for="level" class="filter-form-label">Level Anggota</label>
                            <select id="level" name="level" class="filter-form-input">
                                <option value="">Semua Level</option>
                                <option value="siswa" {{ request('level') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="guru" {{ request('level') === 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="staff" {{ request('level') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                    @endif
                    <div class="filter-form-group">
                        <label for="jenis_sanksi" class="filter-form-label">Jenis Sanksi</label>
                        <select id="jenis_sanksi" name="jenis_sanksi" class="filter-form-input">
                            <option value="">Semua Jenis</option>
                            <option value="keterlambatan"
                                {{ request('jenis_sanksi') === 'keterlambatan' ? 'selected' : '' }}>
                                Keterlambatan</option>
                            <option value="rusak_hilang"
                                {{ request('jenis_sanksi') === 'rusak_hilang' ? 'selected' : '' }}>
                                Rusak/Hilang</option>
                        </select>
                    </div>
                    <div class="filter-buttons-container">
                        <button type="submit" class="btn-download btn-filter">
                            <i class='bx bx-search'></i> Filter
                        </button>
                        @if (auth()->user()->level == 'admin')
                            <a id="resetBtn" href="{{ route('laporan.sanksi.belum_bayar') }}"
                                class="btn-download btn-reset" style="display: none;">
                                <i class='bx bx-refresh'></i> Reset
                            </a>
                        @else
                            <a id="resetBtnNonAdmin" href="{{ route('laporan.sanksi.belum_bayar') }}"
                                class="btn-download btn-reset" style="display: none;">
                                <i class='bx bx-refresh'></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    @if (auth()->user()->level == 'admin')
                        <h3>Daftar Sanksi Belum Bayar</h3>
                    @else
                        <h3>Sanksi Belum Bayar Anda</h3>
                    @endif
                    <div class="menu">
                        <span style="color: var(--dark-grey); font-size: 14px;">
                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk mengunduh data
                        </span>
                    </div>
                </div>

                <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $sanksi->count() }} sanksi belum bayar
                </p>

                @if ($sanksi->count() > 0)
                    <div class="table-responsive p-3">
                        <table id="dataTableExport" class="table align-items-center table-flush table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    @if (auth()->user()->level == 'admin')
                                        <th>Tanggal Sanksi</th>
                                        <th>Nama Peminjam</th>
                                        <th>Level</th>
                                        <th>Judul Buku</th>
                                        <th>Jenis Sanksi</th>
                                        <th>Hari Terlambat</th>
                                        <th>Denda Keterlambatan</th>
                                        <th>Denda Kerusakan</th>
                                        <th>Total Denda</th>
                                        <th>Keterangan</th>
                                    @else
                                        <th>Tanggal Sanksi</th>
                                        <th>Judul Buku</th>
                                        <th>Jenis Sanksi</th>
                                        <th>Hari Terlambat</th>
                                        <th>Denda Keterlambatan</th>
                                        <th>Denda Kerusakan</th>
                                        <th>Total Denda</th>
                                        <th>Keterangan</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sanksi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        @if (auth()->user()->level == 'admin')
                                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $item->peminjaman->user->nama }}</td>
                                            <td>{{ ucfirst($item->peminjaman->user->level) }}</td>
                                        @else
                                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        @endif
                                        <td>{{ $item->peminjaman->buku->judul }}</td>
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
                                        <td>{{ $item->hari_terlambat }} hari</td>
                                        <td>Rp {{ number_format($item->denda_keterlambatan, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</td>
                                        <td><strong>Rp {{ number_format($item->total_denda, 0, ',', '.') }}</strong></td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state"
                        style="text-align: center; padding: 60px 20px; background-color: var(--grey); border-radius: 10px; margin: 20px 0;">
                        <i class='bx bx-info-circle'
                            style="font-size: 64px; color: var(--dark-grey); margin-bottom: 20px; display: block;"></i>
                        @if (auth()->user()->level == 'admin')
                            <h3 style="color: var(--dark); margin-bottom: 10px;">Tidak ada data sanksi belum bayar</h3>
                            <p style="color: var(--dark-grey); margin: 0;">Belum ada sanksi yang belum dibayar dalam
                                sistem.</p>
                        @else
                            <h3 style="color: var(--dark); margin-bottom: 10px;">Tidak ada sanksi belum bayar</h3>
                            <p style="color: var(--dark-grey); margin: 0;">Anda tidak memiliki sanksi yang belum dibayar.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <!-- Script untuk mengontrol tombol reset -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk mengecek apakah ada filter yang aktif di URL
            function hasActiveFilter() {
                const urlParams = new URLSearchParams(window.location.search);

                // Cek parameter GET di URL
                if (urlParams.get('tanggal_mulai')) return true;
                if (urlParams.get('tanggal_akhir')) return true;
                if (urlParams.get('jenis_sanksi')) return true;

                @if (auth()->user()->level == 'admin')
                    if (urlParams.get('level')) return true;
                @endif

                return false;
            }

            // Fungsi untuk toggle tombol reset
            function toggleResetButton() {
                @if (auth()->user()->level == 'admin')
                    var resetBtn = document.getElementById('resetBtn');
                @else
                    var resetBtn = document.getElementById('resetBtnNonAdmin');
                @endif

                if (resetBtn) {
                    if (hasActiveFilter()) {
                        resetBtn.style.display = 'inline-flex';
                    } else {
                        resetBtn.style.display = 'none';
                    }
                }
            }

            // Jalankan saat halaman dimuat
            toggleResetButton();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with export buttons
            var isAdmin = {{ auth()->user()->level == 'admin' ? 'true' : 'false' }};

            // Configure responsive priorities based on user level
            var responsivePriorities = isAdmin ? [{
                    responsivePriority: 1,
                    targets: [0, 2, 4, 9]
                }, // No, Nama, Buku, Total Denda
                {
                    responsivePriorities: 2,
                    targets: [10]
                }, // Keterangan
                {
                    orderable: false,
                    targets: [-1]
                } // Kolom terakhir tidak dapat diurutkan
            ] : [{
                    responsivePriority: 1,
                    targets: [0, 2, 6]
                }, // No, Buku, Total Denda
                {
                    responsivePriority: 2,
                    targets: [7]
                }, // Keterangan
                {
                    orderable: false,
                    targets: [-1]
                } // Kolom terakhir tidak dapat diurutkan
            ];

            $('#dataTableExport').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ], // Sort by the first column (No) in ascending order
                // Tombol export hanya untuk admin
                @if (auth()->user()->level == 'admin')
                    dom: '<"export-buttons-container"B>lfrtip',
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
                    },
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="bx bx-copy"></i><span>Copy</span>',
                            className: 'btn btn-outline-primary btn-sm export-btn',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bx bx-file"></i><span>CSV</span>',
                            className: 'btn btn-outline-success btn-sm export-btn',
                            filename: 'Laporan_Sanksi_Belum_Bayar_{{ date('d-m-Y') }}',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                            className: 'btn btn-outline-success btn-sm export-btn',
                            filename: 'Laporan_Sanksi_Belum_Bayar_{{ date('d-m-Y') }}',
                            exportOptions: {
                                columns: ':visible'
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
                            filename: 'Laporan_Sanksi_Belum_Bayar_{{ date('d-m-Y') }}',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="bx bx-printer"></i><span>Print</span>',
                            className: 'btn btn-outline-warning btn-sm export-btn',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                    ],
                @endif
                columnDefs: responsivePriorities
            });

            // Function to export table data to Word format
            function exportToWord(dt) {
                // Get table data
                const data = dt.buttons.exportData({
                    columns: ':visible'
                });

                // Different styles based on user level
                var columnClasses, documentTitle;

                if (isAdmin) {
                    columnClasses = ['col-no', 'col-tanggal', 'col-nama', 'col-level', 'col-buku',
                        'col-jenis', 'col-hari', 'col-denda-keterlambatan', 'col-denda-kerusakan',
                        'col-total', 'col-keterangan'
                    ];
                    documentTitle = 'Laporan Sanksi Belum Bayar';
                } else {
                    columnClasses = ['col-no', 'col-tanggal', 'col-buku', 'col-jenis', 'col-hari',
                        'col-denda-keterlambatan', 'col-denda-kerusakan', 'col-total', 'col-keterangan'
                    ];
                    documentTitle = 'Sanksi Belum Bayar Anda';
                }

                // Create HTML content for Word document
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
                                font-size: 8px;
                                table-layout: fixed;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 4px;
                                text-align: left;
                                word-wrap: break-word;
                                overflow-wrap: break-word;
                                vertical-align: top;
                            }
                            th {
                                background-color: #f2f2f2;
                                font-weight: bold;
                                font-size: 9px;
                            }`;

                // Add different column widths based on user level
                if (isAdmin) {
                    htmlContent += `
                            /* Admin view column widths */
                            .col-no { width: 4%; }
                            .col-tanggal { width: 12%; }
                            .col-nama { width: 15%; }
                            .col-level { width: 7%; }
                            .col-buku { width: 15%; }
                            .col-jenis { width: 12%; }
                            .col-hari { width: 8%; }
                            .col-denda-keterlambatan { width: 10%; }
                            .col-denda-kerusakan { width: 10%; }
                            .col-total { width: 10%; }
                            .col-keterangan { width: 7%; }`;
                } else {
                    htmlContent += `
                            /* Non-admin view column widths */
                            .col-no { width: 6%; }
                            .col-tanggal { width: 15%; }
                            .col-buku { width: 20%; }
                            .col-jenis { width: 15%; }
                            .col-hari { width: 10%; }
                            .col-denda-keterlambatan { width: 12%; }
                            .col-denda-kerusakan { width: 12%; }
                            .col-total { width: 12%; }
                            .col-keterangan { width: 8%; }`;
                }

                htmlContent += `
                            .text-center { text-align: center; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>${documentTitle}</h2>
                        </div>
                        <div class="date">
                            <p>Data per tanggal {{ date('d/m/Y') }}</p>
                        </div>
                        <table>
                            <thead>
                                <tr>`;

                // Add headers with specific classes
                data.header.forEach(function(header, index) {
                    const className = columnClasses[index] || '';
                    htmlContent += `<th class="${className}">${header}</th>`;
                });

                htmlContent += `
                                </tr>
                            </thead>
                            <tbody>`;

                // Add data rows
                data.body.forEach(function(row) {
                    htmlContent += '<tr>';
                    row.forEach(function(cell, index) {
                        // Clean cell data (remove HTML tags and extra spaces)
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

                // Create blob and download
                const blob = new Blob([htmlContent], {
                    type: 'application/msword'
                });

                const fileName = isAdmin ?
                    'Laporan_Sanksi_Belum_Bayar_{{ date('d-m-Y') }}.doc' :
                    'Sanksi_Belum_Bayar_{{ date('d-m-Y') }}.doc';

                // Use FileSaver.js to download the file
                saveAs(blob, fileName);
            }
        });
    </script>
@endsection
