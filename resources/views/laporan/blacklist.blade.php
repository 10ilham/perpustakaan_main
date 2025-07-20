@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Dashboard Laporan User Blacklist</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a class="active">Laporan Blacklist</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Blacklist -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalBlacklist }}</h2>
                        <p>Total Blacklist</p>
                    </div>
                    <i class='bx bxs-user-x icon'></i>
                </div>
            </div>
            <!-- Card Blacklist Aktif -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $blacklistAktif }}</h2>
                        <p>Blacklist Aktif</p>
                    </div>
                    <i class='bx bxs-error-circle icon'></i>
                </div>
            </div>
            <!-- Card Blacklist Tidak Aktif -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $blacklistTidakAktif }}</h2>
                        <p>Blacklist Selesai</p>
                    </div>
                    <i class='bx bxs-check-circle icon'></i>
                </div>
            </div>
            <!-- Card Statistik Level -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $statistikLevel->count() }}</h2>
                        <p>Level Terdampak</p>
                    </div>
                    <i class='bx bxs-group icon'></i>
                </div>
            </div>
        </div>

        <!-- Filter dan Pencarian -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Filter Laporan Blacklist</h3>
                </div>
                <form method="GET" action="{{ route('laporan.blacklist') }}" class="filter-form-grid">
                    <div class="filter-form-group">
                        <label for="status" class="filter-form-label">Status</label>
                        <select name="status" id="status" class="filter-form-input">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Blacklist Aktif
                            </option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Blacklist
                                Selesai</option>
                        </select>
                    </div>
                    <div class="filter-form-group">
                        <label for="tanggal_mulai" class="filter-form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="filter-form-input">
                    </div>
                    <div class="filter-form-group">
                        <label for="tanggal_selesai" class="filter-form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                            value="{{ request('tanggal_selesai') }}" class="filter-form-input">
                    </div>
                    <div class="filter-buttons-container">
                        <button type="submit" class="btn-download btn-filter">
                            <i class='bx bx-filter'></i> Filter
                        </button>
                        <a id="resetBtn" href="{{ route('laporan.blacklist') }}" class="btn-download btn-reset"
                            style="display: none;">
                            <i class='bx bx-refresh'></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistik berdasarkan Level -->
        @if ($statistikLevel->count() > 0)
            <div class="data">
                <div class="content-data">
                    <div class="head">
                        <h3>Statistik Blacklist per Level</h3>
                    </div>
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                        @foreach ($statistikLevel as $stat)
                            <div
                                style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                                <div style="text-align: center;">
                                    @if ($stat['level'] == 'siswa')
                                        <i class='bx bxs-user'
                                            style="font-size: 3rem; color: #1d4ed8; margin-bottom: 1rem;"></i>
                                    @elseif($stat['level'] == 'guru')
                                        <i class='bx bxs-user-badge'
                                            style="font-size: 3rem; color: #166534; margin-bottom: 1rem;"></i>
                                    @else
                                        <i class='bx bxs-user-detail'
                                            style="font-size: 3rem; color: #92400e; margin-bottom: 1rem;"></i>
                                    @endif
                                    <h4>{{ ucfirst($stat['level']) }}</h4>
                                    <p style="color: var(--dark-grey); margin-bottom: 20px;">
                                        <strong>Total Blacklist:</strong> {{ $stat['total'] }}<br>
                                        <strong>Blacklist Aktif:</strong> <span
                                            style="color: {{ $stat['aktif'] > 0 ? '#dc2626' : '#16a34a' }}">{{ $stat['aktif'] }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabel Daftar Blacklist -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar User Blacklist</h3>
                    <div class="menu">
                        <span style="color: var(--dark-grey); font-size: 14px;">
                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk mengunduh data
                        </span>
                    </div>
                </div>

                <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $blacklists->count() }} data</p>

                <!-- Export Buttons for Admin -->
                @if (Auth::user()->level === 'admin')
                    {{-- Hidden input to indicate admin role for DataTables --}}
                    <input type="hidden" id="level" value="admin">
                @endif

                @if ($blacklists->count() > 0)
                    <table id="dataTableExport" class="display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Jumlah Pembatalan</th>
                                <th>Tanggal Blacklist</th>
                                <th>Tanggal Berakhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blacklists as $index => $blacklist)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $blacklist->user->nama ?? 'User Tidak Ditemukan' }}</td>
                                    <td>{{ $blacklist->user->email ?? '-' }}</td>
                                    <td>
                                        @if ($blacklist->user && $blacklist->user->level == 'siswa')
                                            <span
                                                style="background: #dbeafe; color: #1d4ed8; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                {{ ucfirst($blacklist->user->level) }}
                                            </span>
                                        @elseif($blacklist->user && $blacklist->user->level == 'guru')
                                            <span
                                                style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                {{ ucfirst($blacklist->user->level) }}
                                            </span>
                                        @elseif($blacklist->user && $blacklist->user->level == 'staff')
                                            <span
                                                style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                {{ ucfirst($blacklist->user->level) }}
                                            </span>
                                        @else
                                            <span
                                                style="background: #f3f4f6; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                                Unknown
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $blacklist->cancelled_bookings_count }}</td>
                                    <td>{{ $blacklist->blacklisted_at ? $blacklist->blacklisted_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>{{ $blacklist->blacklist_expires_at ? $blacklist->blacklist_expires_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>
                                        @if ($blacklist->is_active && $blacklist->blacklist_expires_at && $blacklist->blacklist_expires_at->isFuture())
                                            <span
                                                style="background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Aktif</span>
                                        @else
                                            <span
                                                style="background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-danger delete-btn delete-blacklist-btn"
                                                data-bs-toggle="modal" data-bs-target="#deleteBlacklistModal"
                                                data-id="{{ $blacklist->id }}"
                                                data-nama="{{ $blacklist->user->nama ?? 'User Tidak Ditemukan' }}"
                                                data-status="{{ $blacklist->is_active && $blacklist->blacklist_expires_at && $blacklist->blacklist_expires_at->isFuture() ? 'Aktif' : 'Selesai' }}"
                                                title="Hapus Blacklist">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class='bx bx-data' style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                        <h3>Tidak ada data blacklist</h3>
                        <p>Belum ada user yang masuk dalam daftar blacklist dengan filter yang dipilih.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus Blacklist -->
    <div class="modal fade" id="deleteBlacklistModal" tabindex="-1" aria-labelledby="deleteBlacklistModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBlacklistModalLabel">
                        <i class="fas fa-trash me-2"></i> Konfirmasi Hapus Blacklist
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm mb-4 p-3">
                        <h6 class="alert-heading mb-2" style="font-size: 15px">
                            <i class="fas fa-exclamation-triangle me-1"></i> Perhatian:
                        </h6>
                        <p class="mb-0">Apakah Anda yakin ingin menghapus blacklist ini? User akan bisa melakukan booking
                            kembali setelah blacklist dihapus.</p>
                    </div>

                    <div class="card border-0 shadow-sm mb-0">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <strong>Nama User:</strong> <span id="blacklistUserNama"></span>
                            </div>
                            <div class="mb-2">
                                <strong>Status Blacklist:</strong> <span id="blacklistStatus"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Batal
                        </button>
                        <form id="delete-blacklist-form" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg px-4">
                                <i class="fas fa-trash me-2"></i> Hapus Blacklist
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
            if (document.querySelector('input[name="tanggal_mulai"]').value ||
                document.querySelector('input[name="tanggal_selesai"]').value ||
                document.querySelector('select[name="status"]').value) {
                document.getElementById('resetBtn').style.display = '';
            }

            // Handle delete blacklist modal
            const deleteBlacklistButtons = document.querySelectorAll('.delete-blacklist-btn');
            const deleteBlacklistForm = document.getElementById('delete-blacklist-form');
            const blacklistUserNama = document.getElementById('blacklistUserNama');
            const blacklistStatus = document.getElementById('blacklistStatus');

            deleteBlacklistButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const blacklistId = this.getAttribute('data-id');
                    const userName = this.getAttribute('data-nama');
                    const status = this.getAttribute('data-status');

                    // Update modal content
                    blacklistUserNama.textContent = userName;
                    blacklistStatus.innerHTML = status === 'Aktif' ?
                        '<span style="background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Aktif</span>' :
                        '<span style="background: #dcfce7; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">Selesai</span>';

                    // Update form action
                    deleteBlacklistForm.action = `/laporan/blacklist/${blacklistId}`;
                });
            });
        });
    </script>
@endsection
