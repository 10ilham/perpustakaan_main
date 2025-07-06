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
                        @if (auth()->user()->level == 'admin')
                            <a id="resetBtn" href="{{ route('laporan.sanksi.sudah_bayar') }}"
                                class="btn-download btn-reset" style="display: none;">
                                <i class='bx bx-refresh'></i> Reset
                            </a>
                        @else
                            <a id="resetBtnNonAdmin" href="{{ route('laporan.sanksi.sudah_bayar') }}"
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
                        <h3>Daftar Sanksi Sudah Bayar</h3>
                    @else
                        <h3>Sanksi Sudah Bayar Anda</h3>
                    @endif
                    <div class="menu">
                        @if (auth()->user()->level == 'admin')
                            <span style="color: var(--dark-grey); font-size: 14px;">
                                <i class='bx bx-info-circle'></i>Gunakan tombol export di bawah tabel untuk mengunduh data
                            </span>
                        @else
                            <span style="color: var(--dark-grey); font-size: 14px;">
                                <i class='bx bx-info-circle'></i>Data sanksi yang sudah dibayar akan ditampilkan di sini
                            </span>
                        @endif
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
    <script src="{{ asset('assets/js/laporan/laporan.js') }}"></script>
@endsection
