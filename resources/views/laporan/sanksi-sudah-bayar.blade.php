@extends('layouts.app')

@section('content')
    <main>
        @if (auth()->user()->level == 'admin')
            <h1 class="title">Laporan Sanksi Sudah Bayar</h1>
        @else
            <h1 class="title">Sanksi Sudah Bayar</h1>
        @endif
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="divider">/</li>
                <li><a href="{{ route('laporan.sanksi') }}">Laporan Sanksi</a></li>
                <li class="divider">/</li>
                <li><a class="active">Sudah Bayar</a></li>
            @else
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                <li class="divider">/</li>
                <li><a class="active">Sanksi Sudah Bayar</a></li>
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
                <form method="GET" action="{{ route('laporan.sanksi.sudah_bayar') }}" class="filter-form-grid">
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
                            <option value="rusak_parah" {{ request('jenis_sanksi') === 'rusak_parah' ? 'selected' : '' }}>
                                Rusak Parah/Hilang</option>
                        </select>
                    </div>
                    <div class="filter-buttons-container">
                        <button type="submit" class="btn-download btn-filter">
                            <i class='bx bx-search'></i> Filter
                        </button>
                        <a href="{{ route('laporan.sanksi.sudah_bayar') }}" class="btn-download btn-reset">
                            <i class='bx bx-refresh'></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    @if (auth()->user()->level == 'admin')
                        <h3>Daftar Sanksi Sudah Bayar</h3>
                    @else
                        <h3>Sanksi Sudah Bayar Anda</h3>
                    @endif
                    <div class="menu">
                        <span style="color: var(--dark-grey); font-size: 14px;">
                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk mengunduh data
                        </span>
                    </div>
                </div>

                <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $sanksi->count() }} sanksi sudah bayar
                </p>

                @if ($sanksi->count() > 0)
                    <div class="table-responsive p-3">
                        <table id="dataTableExport" class="table align-items-center table-flush table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sanksi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $item->peminjaman->user->nama }}</td>
                                        <td>
                                            @if ($item->peminjaman->user->level === 'siswa')
                                                <span class="badge" style="color: #007bff; font-weight: bold">Siswa</span>
                                            @elseif ($item->peminjaman->user->level === 'guru')
                                                <span class="badge" style="color: #28a745; font-weight: bold">Guru</span>
                                            @else
                                                <span class="badge" style="color: #ffc107; font-weight: bold">Staff</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->peminjaman->buku->judul }}</td>
                                        <td>
                                            @php
                                                $jenisSanksi = explode(',', $item->jenis_sanksi);
                                                $displayJenis = [];
                                                foreach ($jenisSanksi as $jenis) {
                                                    if ($jenis === 'keterlambatan') {
                                                        $displayJenis[] = 'Keterlambatan';
                                                    } elseif ($jenis === 'rusak_parah') {
                                                        $displayJenis[] = 'Rusak Parah/Hilang';
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $displayJenis) }}
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

                    <!-- Export buttons -->
                    <div class="mt-3 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="export-buttons">
                                    <button type="button" class="btn-download" onclick="exportToPDF()">
                                        <i class='bx bxs-file-pdf'></i> Export PDF
                                    </button>
                                    <button type="button" class="btn-download" onclick="exportToExcel()">
                                        <i class='bx bxs-file'></i> Export Excel
                                    </button>
                                    <button type="button" class="btn-download" onclick="printTable()">
                                        <i class='bx bxs-printer'></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state"
                        style="text-align: center; padding: 60px 20px; background-color: var(--grey); border-radius: 10px; margin: 20px 0;">
                        <i class='bx bx-info-circle'
                            style="font-size: 64px; color: var(--dark-grey); margin-bottom: 20px; display: block;"></i>
                        @if (auth()->user()->level == 'admin')
                            <h3 style="color: var(--dark); margin-bottom: 10px;">Tidak ada data sanksi sudah bayar</h3>
                            <p style="color: var(--dark-grey); margin: 0;">Belum ada sanksi yang sudah dibayar dalam
                                sistem.</p>
                        @else
                            <h3 style="color: var(--dark); margin-bottom: 10px;">Tidak ada sanksi sudah bayar</h3>
                            <p style="color: var(--dark-grey); margin: 0;">Anda tidak memiliki sanksi yang sudah dibayar.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Export to PDF
        function exportToPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            // Add title
            doc.setFontSize(16);
            doc.text('Laporan Sanksi Sudah Bayar', 20, 20);

            // Add timestamp
            doc.setFontSize(10);
            doc.text(`Dicetak pada: ${new Date().toLocaleString('id-ID')}`, 20, 30);

            // Prepare table data
            const tableData = [];
            const table = document.getElementById('dataTableExport');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                const rowData = [];
                cells.forEach((cell, cellIndex) => {
                    if (cellIndex < 11) { // Limit columns to fit in PDF
                        rowData.push(cell.textContent.trim());
                    }
                });
                tableData.push(rowData);
            });

            // Add table
            doc.autoTable({
                head: [
                    ['No', 'Tanggal', 'Nama', 'Level', 'Buku', 'Jenis Sanksi', 'Hari Terlambat',
                        'Denda Keterlambatan', 'Denda Kerusakan', 'Total Denda', 'Keterangan'
                    ]
                ],
                body: tableData,
                startY: 40,
                styles: {
                    fontSize: 8,
                    cellPadding: 2
                }
            });

            // Save the PDF
            doc.save('laporan-sanksi-sudah-bayar.pdf');
        }

        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById('dataTableExport');
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: 'Sanksi Sudah Bayar'
            });
            XLSX.writeFile(workbook, 'laporan-sanksi-sudah-bayar.xlsx');
        }

        // Print table
        function printTable() {
            const printWindow = window.open('', '', 'width=800,height=600');
            const table = document.getElementById('dataTableExport').outerHTML;

            printWindow.document.write(`
                <html>
                <head>
                    <title>Laporan Sanksi Sudah Bayar</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .badge { padding: 2px 6px; border-radius: 3px; font-size: 12px; }
                        h1 { color: #333; }
                        .timestamp { color: #666; font-size: 12px; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <h1>Laporan Sanksi Sudah Bayar</h1>
                    <p class="timestamp">Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
                    ${table}
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.print();
        }
    </script>
@endsection
