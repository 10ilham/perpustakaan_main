@extends('layouts.app')

@section('content')
    <main>
        @if (auth()->user()->level == 'admin')
            <h1 class="title">Laporan Sudah Dikembalikan</h1>
        @else
            <h1 class="title">Laporan Riwayat Peminjaman Anda (Sudah Dikembalikan)</h1>
        @endif
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @else
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @endif
            <li class="divider">/</li>
            <li><a href="{{ route('laporan.index') }}">Laporan</a></li>
            <li class="divider">/</li>
            <li><a class="active">Sudah Dikembalikan</a></li>
        </ul>

        {{-- Button untuk kembali ke dashboard laporan --}}
        @if (auth()->user()->level == 'admin')
            <div class="back-button">
                <a href="{{ route('laporan.index') }}" class="btn-secondary-laporan">
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
                @if (auth()->user()->level == 'admin')
                    <form method="GET" action="{{ route('laporan.sudah_kembali') }}" class="filter-form-grid">
                        <div class="filter-form-group">
                            <label for="tanggal_mulai" class="filter-form-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}" class="filter-form-input">
                        </div>
                        <div class="filter-form-group">
                            <label for="tanggal_selesai" class="filter-form-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}" class="filter-form-input">
                        </div>
                        <div class="filter-form-group">
                            <label for="level" class="filter-form-label">Level User</label>
                            <select id="level" name="level" class="filter-form-input">
                                <option value="">Semua Level</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level }}" {{ request('level') === $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-form-group">
                            <label for="status" class="filter-form-label">Status</label>
                            <select id="status" name="status" class="filter-form-input">
                                <option value="">Semua Status</option>
                                <option value="tepat_waktu" {{ request('status') === 'tepat_waktu' ? 'selected' : '' }}>
                                    Tepat Waktu</option>
                                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>
                                    Terlambat</option>
                            </select>
                        </div>
                        <div class="filter-buttons-container">
                            <button type="submit" class="btn-download btn-filter">
                                <i class='bx bx-search'></i> Filter
                            </button>
                            <a id="resetBtn" href="{{ route('laporan.sudah_kembali') }}" class="btn-download btn-reset"
                                style="display: none;">
                                <i class='bx bx-refresh'></i> Reset
                            </a>
                        </div>
                    </form>
                @else
                    <form method="GET" action="{{ route('laporan.sudah_kembali') }}" class="filter-form-grid">
                        <div class="filter-form-group">
                            <label for="tanggal_mulai" class="filter-form-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}" class="filter-form-input">
                        </div>
                        <div class="filter-form-group">
                            <label for="tanggal_selesai" class="filter-form-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}" class="filter-form-input">
                        </div>
                        <div class="filter-form-group">
                            <label for="status" class="filter-form-label">Status</label>
                            <select id="status" name="status" class="filter-form-input">
                                <option value="">Semua Status</option>
                                <option value="tepat_waktu" {{ request('status') === 'tepat_waktu' ? 'selected' : '' }}>
                                    Tepat Waktu</option>
                                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>
                                    Terlambat</option>
                            </select>
                        </div>

                        <div class="filter-buttons-container">
                            <button type="submit" class="btn-download btn-filter">
                                <i class='bx bx-search'></i> Filter
                            </button>
                            <a id="resetBtnNonAdmin" href="{{ route('laporan.sudah_kembali') }}"
                                class="btn-download btn-reset" style="display: none;">
                                <i class='bx bx-refresh'></i> Reset
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Results Table -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    @if (auth()->user()->level == 'admin')
                        <h3>Daftar Buku Sudah Dikembalikan</h3>
                        <div class="menu">
                            <span style="color: var(--dark-grey); font-size: 14px;">
                                <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk mengunduh data
                            </span>
                        </div>
                    @else
                        <h3>Daftar Riwayat Buku Sudah Anda Kembalikan</h3>
                        <div class="menu">
                            <span style="color: var(--dark-grey); font-size: 14px;">
                                <i class='bx bx-info-circle'></i> Ini adalah daftar buku yang sudah Anda kembalikan
                            </span>
                        </div>
                    @endif
                </div>

                @if (auth()->user()->level == 'admin')
                    <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $peminjamanSudahKembali->count() }}
                        peminjaman</p>
                @else
                    <p style="color: var(--dark-grey); margin-bottom: 20px;">Total: {{ $peminjamanSudahKembali->count() }}
                        buku yang sudah Anda kembalikan</p>
                @endif

                @if ($peminjamanSudahKembali->count() > 0)
                    <div class="table-responsive p-3">
                        <table id="dataTableExport" class="table align-items-center table-flush table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    @if (auth()->user()->level == 'admin')
                                        <th>Nama Peminjam</th>
                                        <th>Email</th>
                                        <th>Level</th>
                                    @endif
                                    <th>Buku</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Tanggal Dikembalikan</th>
                                    <th>Status</th>
                                    {{-- <th style="display: none;">Catatan</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peminjamanSudahKembali as $index => $peminjaman)
                                    @php
                                        $tanggalKembali = \Carbon\Carbon::parse($peminjaman->tanggal_kembali);
                                        $tanggalDikembalikan = \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian);
                                        $statusPengembalian = 'Tepat Waktu';
                                        $statusClass = 'completed';

                                        if ($tanggalDikembalikan->gt($tanggalKembali)) {
                                            $hariTerlambat = $tanggalDikembalikan->diffInDays($tanggalKembali);
                                            $statusPengembalian = 'Terlambat ' . $hariTerlambat . ' hari';
                                            $statusClass = 'pending';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        @if (auth()->user()->level == 'admin')
                                            <td>
                                                <span class="status">{{ $peminjaman->user->nama }}</span>
                                            </td>
                                            <td>
                                                <span class="status">{{ $peminjaman->user->email }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="status completed">{{ ucfirst($peminjaman->user->level) }}</span>
                                            </td>
                                        @endif
                                        <td>
                                            <span class="status">{{ $peminjaman->buku->judul }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $statusClass === 'completed' ? '#28a745' : '#ffc107' }}; color: {{ $statusClass === 'completed' ? 'white' : 'black' }};">
                                                {{ $statusPengembalian }}
                                            </span>
                                        </td>
                                        {{-- <td style="display: none;"> {{ $peminjaman->catatan ?? '-' }}</td> --}}
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('peminjaman.detail', ['id' => $peminjaman->id, 'ref' => 'laporan_sudah_kembali']) }}"
                                                    class="btn btn-sm btn-info" title="Detail">
                                                    <i class='bx bx-info-circle'></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px;">
                        <i class='bx bx-info-circle'
                            style="font-size: 48px; color: var(--dark-grey); margin-bottom: 16px;"></i>
                        @if (auth()->user()->level == 'admin')
                            <p style="color: var(--dark-grey);">Tidak ada data peminjaman yang sudah dikembalikan dengan
                                filter tersebut.</p>
                        @else
                            <p style="color: var(--dark-grey);">Anda belum memiliki riwayat peminjaman yang sudah
                                dikembalikan dengan filter tersebut.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection


@section('scripts')
    <script src="{{ asset('assets/js/laporan/laporan.js') }}"></script>
@endsection
