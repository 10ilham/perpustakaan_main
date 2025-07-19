@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Laporan Buku Masuk dan Keluar</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a href="{{ route('laporan.index') }}">Laporan</a></li>
            <li class="divider">/</li>
            <li><a class="active">Buku Masuk/Keluar</a></li>
        </ul>

        {{-- Button untuk kembali ke dashboard laporan --}}
        <div class="back-button">
            <a href="{{ route('laporan.index') }}" class="btn-secondary-laporan">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>

        <!-- Filter Form -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Filter Laporan</h3>
                </div>
                <form method="GET" action="{{ route('laporan.buku_log') }}" class="filter-form-grid">
                    <div class="filter-form-group">
                        <label for="filter" class="filter-form-label">Tipe</label>
                        <select id="filter" name="filter" class="filter-form-input">
                            <option value="semua" {{ $filter == 'semua' ? 'selected' : '' }}>Semua</option>
                            <option value="masuk" {{ $filter == 'masuk' ? 'selected' : '' }}>Buku Masuk</option>
                            <option value="keluar" {{ $filter == 'keluar' ? 'selected' : '' }}>Buku Keluar</option>
                        </select>
                    </div>
                    <div class="filter-form-group">
                        <label for="start_date" class="filter-form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="filter-form-input">
                    </div>
                    <div class="filter-form-group">
                        <label for="end_date" class="filter-form-label">Tanggal Selesai</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                            class="filter-form-input">
                    </div>
                    <div class="filter-buttons-container">
                        <button type="submit" class="btn-download btn-filter">
                            <i class='bx bx-search'></i> Filter
                        </button>
                        <a id="resetBtn" href="{{ route('laporan.buku_log') }}" class="btn-download btn-reset"
                            style="{{ request('filter') || request('start_date') || request('end_date') ? '' : 'display: none;' }}">
                            <i class='bx bx-refresh'></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>
                        @if ($filter == 'masuk')
                            Laporan Buku Masuk
                        @elseif($filter == 'keluar')
                            Laporan Buku Keluar
                        @else
                            Laporan Buku Masuk dan Keluar
                        @endif
                    </h3>
                    <div class="menu">
                        <span style="color: var(--dark-grey); font-size: 14px;">
                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk mengunduh data
                        </span>
                    </div>
                </div>

                <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $logs->count() }} data</p>

                {{-- Hidden input to indicate admin role for DataTables --}}
                <input type="hidden" id="level" value="admin">

                @if ($logs->count() > 0)
                    <div class="table-responsive p-3">
                        <table id="dataTableExport" class="table align-items-center table-flush table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th class="col-no">No.</th>
                                    <th class="col-tanggal">Tanggal</th>
                                    <th class="col-kode">Kode Buku</th>
                                    <th class="col-judul">Judul Buku</th>
                                    <th class="col-tipe">Tipe</th>
                                    <th class="col-jumlah">Jumlah</th>
                                    <th class="col-alasan">Alasan</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $index => $log)
                                    <tr>
                                        <td class="col-no">{{ $index + 1 }}</td>
                                        <td class="col-tanggal">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="col-kode">{{ $log->kode_buku }}</td>
                                        <td class="col-judul">{{ $log->judul_buku }}</td>
                                        <td class="col-tipe">
                                            @if ($log->tipe == 'masuk')
                                                <span class="badge"
                                                    style="background-color: #28a745; color: white;">Masuk</span>
                                            @else
                                                <span class="badge"
                                                    style="background-color: #dc3545; color: white;">Keluar</span>
                                            @endif
                                        </td>
                                        <td class="col-jumlah">{{ $log->jumlah }}</td>
                                        <td class="col-alasan">{{ $log->alasan }}</td>
                                        <td class="col-aksi">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger delete-btn delete-log-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteLogModal"
                                                    data-id="{{ $log->id }}" data-judul="{{ $log->judul_buku }}"
                                                    data-tipe="{{ $log->tipe }}" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $logs->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 40px;">
                        <i class='bx bx-info-circle'
                            style="font-size: 48px; color: var(--dark-grey); margin-bottom: 16px;"></i>
                        <p style="color: var(--dark-grey);">Tidak ada data buku masuk/keluar dengan filter tersebut.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus Log -->
    <div class="modal fade" id="deleteLogModal" tabindex="-1" aria-labelledby="deleteLogModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLogModalLabel">
                        <i class="fas fa-trash me-2"></i> Konfirmasi Hapus Log
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm mb-4 p-3">
                        <h6 class="alert-heading mb-2" style="font-size: 15px">
                            <i class="fas fa-exclamation-triangle me-1"></i> Perhatian:
                        </h6>
                        <p class="mb-0">Apakah Anda yakin ingin menghapus log ini? Data yang dihapus tidak dapat
                            dikembalikan.</p>
                    </div>

                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <strong>Buku:</strong> <span id="logBukuJudul"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Tipe:</strong> <span id="logTipe"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Batal
                        </button>
                        <form id="delete-log-form" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg px-4">
                                <i class="fas fa-trash me-2"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/laporan/laporan.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show reset button if any filter is applied
            if (document.querySelector('input[name="start_date"]').value ||
                document.querySelector('input[name="end_date"]').value ||
                document.querySelector('select[name="filter"]').value !== 'semua') {
                document.getElementById('resetBtn').style.display = '';
            }

            // Handle delete log modal
            const deleteLogButtons = document.querySelectorAll('.delete-log-btn');
            const deleteLogForm = document.getElementById('delete-log-form');
            const logBukuJudul = document.getElementById('logBukuJudul');
            const logTipe = document.getElementById('logTipe');

            deleteLogButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const logId = this.getAttribute('data-id');
                    const bukuJudul = this.getAttribute('data-judul');
                    const tipe = this.getAttribute('data-tipe');

                    // Update modal content
                    logBukuJudul.textContent = bukuJudul;
                    logTipe.innerHTML = tipe === 'masuk' ?
                        '<span class="badge" style="background-color: #28a745; color: white;">Masuk</span>' :
                        '<span class="badge" style="background-color: #dc3545; color: white;">Keluar</span>';

                    // Update form action
                    deleteLogForm.action = `/laporan/buku-log/${logId}`;
                });
            });
        });
    </script>
@endsection
