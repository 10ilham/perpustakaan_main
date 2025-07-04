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
        <!-- Filter untuk Sanksi-->

        <div class="filter">
            <div class="card">
                <div class="head">
                    <h3>Filter Sanksi</h3>
                </div>
                <div class="form-group" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                    {{--  Level admin --}}
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
                        <option value="rusak_parah">Rusak Parah atau Hilang</option>
                    </select>

                    <select id="filterStatus" class="form-control" style="max-width: 180px; height: 40px;">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sudah_bayar">Sudah Bayar</option>
                    </select>

                    <button type="button" class="btn btn-primary" style="padding: 5px 15px;" onclick="applyFilters()">
                        <i class='bx bx-filter'></i> Filter
                    </button>

                    <button type="button" class="btn btn-secondary" style="padding: 5px 15px;" onclick="resetFilters()">
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
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Jenis Sanksi</th>
                                <th>Hari Terlambat</th>
                                <th>Denda Keterlambatan</th>
                                <th>Denda Kerusakan</th>
                                <th>Total Denda</th>
                                <th>Status Bayar</th>
                                <th>Tanggal Sanksi</th>
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
                                        <small class="text-muted">{{ ucfirst($item->peminjaman->user->level) }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $item->peminjaman->buku->judul }}</strong><br>
                                        <small class="text-muted">{{ $item->peminjaman->buku->kode_buku }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $jenisSanksi = explode(',', $item->jenis_sanksi);
                                        @endphp
                                        @foreach ($jenisSanksi as $jenis)
                                            <span
                                                class="badge
                                                @if ($jenis == 'keterlambatan') badge-warning
                                                @elseif($jenis == 'rusak_parah') badge-danger
                                                @else badge-dark @endif
                                            ">
                                                {{ ucfirst(str_replace('_', ' ', $jenis)) }}
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
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Belum Dibayar</h6>
                                        <h4 class="text-danger">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'belum_bayar')->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Sudah Dibayar</h6>
                                        <h4 class="text-success">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'sudah_bayar')->sum('total_denda'), 0, ',', '.') }}
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
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#sanksiTable').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                },
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                "order": [
                    [9, "desc"]
                ], // Order by tanggal sanksi (column 9) descending
                "columnDefs": [{
                        "orderable": false,
                        "targets": [
                            @if (auth()->user()->level == 'admin')
                                10
                            @else
                                -1
                            @endif
                        ]
                    } // Disable sorting on action column
                ]
            });

            // Apply filters function - dipanggil ketika tombol Filter diklik
            window.applyFilters = function() {
                @if (auth()->user()->level == 'admin')
                    // Filter by Jenis Anggota (hanya untuk admin)
                    var filterAnggota = $('#filterAnggota').val();
                    if (filterAnggota === '') {
                        table.column(1).search('');
                    } else {
                        table.column(1).search(filterAnggota, false, false);
                    }
                @endif

                // Filter by Jenis Sanksi
                var filterSanksi = $('#filterSanksi').val();
                if (filterSanksi === '') {
                    table.column(3).search('');
                } else {
                    var searchTerm = filterSanksi === 'keterlambatan' ? 'Keterlambatan' : 'Rusak parah';
                    table.column(3).search(searchTerm, false, false);
                }

                // Filter by Status Bayar
                var filterStatus = $('#filterStatus').val();
                if (filterStatus === '') {
                    table.column(8).search('');
                } else {
                    var searchTerm = filterStatus === 'belum_bayar' ? 'Belum Bayar' : 'Sudah Bayar';
                    table.column(8).search(searchTerm, false, false);
                }

                // Apply all filters
                table.draw();
            };

            // Reset filters function
            window.resetFilters = function() {
                @if (auth()->user()->level == 'admin')
                    $('#filterAnggota').val('');
                @endif
                $('#filterSanksi').val('');
                $('#filterStatus').val('');
                table.search('').columns().search('').draw();
            };

            // Show payment confirmation modal
            window.showPaymentModal = function(sanksiId) {
                // Set form action
                $('#paymentForm').attr('action', '{{ route('sanksi.bayar', ':id') }}'.replace(':id',
                    sanksiId));

                // Show modal
                $('#paymentModal').modal('show');
            };
        });
    </script>
@endsection

@section('styles')
    <style>
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

        /* Filter styling - konsisten dengan halaman peminjaman */
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

        /* Button styling untuk filter */
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

        /* Form control styling */
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

        /* DataTable custom styling */
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

        /* Modal Payment Styling */
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

        .payment-info .alert {
            border-radius: 8px;
            border: none;
            background: #e8f4fd;
            color: #0c5aa6;
        }

        .payment-details table td {
            padding: 0.5rem 0;
            vertical-align: top;
        }

        .payment-details table td:first-child {
            width: 120px;
            color: #666;
        }

        .confirmation-text {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        .modal-footer .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
    </style>
@endsection
