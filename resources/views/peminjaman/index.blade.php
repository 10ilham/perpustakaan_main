@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Peminjaman Buku</div>
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
            <li><a class="active">Peminjaman</a></li>
        </ul>

        <!-- Info Cards -->
        <div class="info-data">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalPeminjaman }}</h2>
                        <p>Total Peminjaman {{ request('user_type') ? ucfirst(request('user_type')) : '' }}
                            {{ request('status') }}</p>
                    </div>
                    <i class='bx bxs-book-bookmark icon'></i>
                </div>
            </div>

            <!-- Card Dipinjam -->
            <div class="card">
                <div class="head">
                    <div>
                        @if (request('status') == 'Dikembalikan')
                            <h2>{{ $peminjaman->where('status', 'Dipinjam')->count() }}</h2>
                            <p>Sedang Dipinjam</p>
                        @elseif(request('status') == 'Terlambat')
                            <h2>{{ $peminjaman->where('status', 'Terlambat')->count() }}</h2>
                            <p>Sedang Dipinjam & Terlambat</p>
                        @elseif(request('status') == 'Diproses')
                            <h2>{{ $peminjaman->where('status', 'Dipinjam')->count() }}</h2>
                            <p>Sedang Dipinjam</p>
                        @elseif(request('status') == 'Dibatalkan')
                            <h2>{{ $peminjaman->where('status', 'Dipinjam')->count() }}</h2>
                            <p>Sedang Dipinjam</p>
                        @else
                            <h2>{{ $dipinjam }}</h2>
                            <p>Sedang Dipinjam</p>
                        @endif
                    </div>
                    <i class='bx bxs-book icon'></i>
                </div>
            </div>

            <!-- Card Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        @if (request('status') == 'Dipinjam')
                            <h2>{{ $peminjaman->where('status', 'Dikembalikan')->count() }}</h2>
                            <p>Dikembalikan</p>
                        @elseif(request('status') == 'Terlambat')
                            <h2>{{ $peminjaman->where('status', 'Dikembalikan')->where('is_terlambat', true)->count() }}
                            </h2>
                            <p>Dikembalikan & Terlambat</p>
                        @elseif(request('status') == 'Diproses')
                            <h2>{{ $peminjaman->where('status', 'Dikembalikan')->count() }}</h2>
                            <p>Dikembalikan</p>
                        @elseif(request('status') == 'Dibatalkan')
                            <h2>{{ $peminjaman->where('status', 'Dikembalikan')->count() }}</h2>
                            <p>Dikembalikan</p>
                        @else
                            <h2>{{ $dikembalikan }}</h2>
                            <p>Dikembalikan</p>
                        @endif
                    </div>
                    <i class='bx bx-check-circle icon'></i>
                </div>
            </div>

            <!-- Card Terlambat -->
            <div class="card">
                <div class="head">
                    <div>
                        @if (request('status') == 'Dipinjam')
                            <h2>{{ $peminjaman->where('status', 'Terlambat')->count() }}</h2>
                            <p>Terlambat & Belum Dikembalikan</p>
                        @elseif(request('status') == 'Dikembalikan')
                            <h2>{{ $peminjaman->where('status', 'Dikembalikan')->where('is_terlambat', true)->count() }}
                            </h2>
                            <p>Terlambat Dikembalikan </p>
                        @elseif(request('status') == 'Terlambat')
                            <h2>{{ $terlambat }}</h2>
                            <p>Total Terlambat</p>
                        @elseif(request('status') == 'Diproses')
                            <h2>{{ $peminjaman->where('status', 'Terlambat')->count() }}</h2>
                            <p>Terlambat</p>
                        @elseif(request('status') == 'Dibatalkan')
                            <h2>{{ $peminjaman->where('status', 'Terlambat')->count() }}</h2>
                            <p>Terlambat</p>
                        @else
                            <h2>{{ $terlambat }}</h2>
                            <p>Terlambat</p>
                        @endif
                    </div>
                    <i class='bx bxs-time icon'></i>
                </div>
            </div>

            <!-- Card Diproses -->
            <div class="card">
                <div class="head">
                    <div>
                        @if (request('status') == 'Diproses')
                            <h2>{{ isset($diproses) ? $diproses : $peminjaman->where('status', 'Diproses')->count() }}</h2>
                            <p>Sedang Diproses</p>
                        @else
                            <h2>{{ $peminjaman->where('status', 'Diproses')->count() }}</h2>
                            <p>Sedang Diproses</p>
                        @endif
                    </div>
                    <i class='bx bx-loader icon'></i>
                </div>
            </div>

            <!-- Card Dibatalkan -->
            <div class="card">
                <div class="head">
                    <div>
                        @if (request('status') == 'Dibatalkan')
                            <h2>{{ isset($dibatalkan) ? $dibatalkan : $peminjaman->where('status', 'Dibatalkan')->count() }}
                            </h2>
                            <p>Dibatalkan</p>
                        @else
                            <h2>{{ $peminjaman->where('status', 'Dibatalkan')->count() }}</h2>
                            <p>Dibatalkan</p>
                        @endif
                    </div>
                    <i class='bx bx-x-circle icon'></i>
                </div>
            </div>
        </div>

        <!-- Filter untuk Admin -->
        @if (auth()->user()->level == 'admin')
            <div class="filter">
                <div class="card">
                    <div class="head">
                        <h3>Filter Peminjaman</h3>
                    </div>
                    <form action="{{ route('peminjaman.index') }}" method="GET" class="form-group"
                        style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                        <select name="user_type" id="user_type" class="form-control" style="max-width: 180px;">
                            <option value="">Semua Anggota</option>
                            <option value="siswa" {{ request('user_type') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="guru" {{ request('user_type') == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="staff" {{ request('user_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>

                        {{-- Filter status --}}
                        <select name="status" id="status" class="form-control" style="max-width: 180px;">
                            <option value="">Semua Status</option>
                            <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses
                            </option>
                            <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam
                            </option>
                            <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>
                                Dikembalikan</option>
                            <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat
                            </option>
                            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>

                        <!-- Filter Rentang Waktu -->
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <label for="start_date">Dari:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}" style="width: 150px;">
                        </div>

                        <div style="display: flex; align-items: center; gap: 5px;">
                            <label for="end_date">Sampai:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}" style="width: 150px;">
                        </div>

                        <button type="submit" class="btn-download btn-filter" style="padding: 5px 15px;">
                            <i class='bx bx-filter'></i> Filter
                        </button>

                        @if (request('user_type') || request('status') || request('start_date') || request('end_date'))
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary" style="padding: 5px 15px;">
                                <i class='bx bx-reset'></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Global Export Section for All Tables Combined -->
            <div class="global-export-section">
                <div class="data">
                    <div class="content-data">
                        <div class="head">
                            <h3>Export Semua Data Peminjaman</h3>
                            <div class="menu">
                                <span style="color: var(--dark-grey); font-size: 14px;">
                                    <i class='bx bx-info-circle'></i> Export data dari semua tabel (Siswa, Guru, Staff)
                                    dalam satu file
                                </span>
                            </div>
                        </div>

                        <div class="export-all-buttons-container">
                            <button id="exportAllToExcel" class="btn btn-outline-success export-btn">
                                <i class="bx bx-file-blank"></i><span>Excel Semua Data</span>
                            </button>
                            <button id="exportAllToWord" class="btn btn-outline-info export-btn">
                                <i class="bx bxs-file-doc"></i><span>Word Semua Data</span>
                            </button>
                            <button id="exportAllToPDF" class="btn btn-outline-danger export-btn">
                                <i class="bx bxs-file-pdf"></i><span>PDF Semua Data</span>
                            </button>
                            <button id="exportAllToCSV" class="btn btn-outline-success export-btn">
                                <i class="bx bx-file"></i><span>CSV Semua Data</span>
                            </button>
                            <button id="printAllData" class="btn btn-outline-warning export-btn">
                                <i class="bx bx-printer"></i><span>Print Semua Data</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tampilan tabel untuk Halaman Admin -->
            <div class="tab-content" id="userTypesContent">
                @if (!request('user_type') || request('user_type') == 'siswa')
                    <div class="tab-pane fade show active" id="siswa-peminjaman" role="tabpanel"
                        aria-labelledby="siswa-tab">
                        <div class="data">
                            <div class="content-data">
                                <div class="head">
                                    <h3>Daftar Peminjaman Siswa</h3>
                                    <div class="menu">
                                        <span style="color: var(--dark-grey); font-size: 14px;">
                                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk
                                            mengunduh data
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive p-3">
                                    <table id="tableSiswa" class="table align-items-center table-flush table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>No. Peminjaman</th>
                                                <th>Judul Buku</th>
                                                <th>Nama Peminjam</th>
                                                <th>Tanggal Pinjam</th>
                                                <th>Tanggal Batas Kembali</th>
                                                <th>Tanggal Pengembalian</th>
                                                <th>Status</th>
                                                <th style="display: none;">Catatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $siswaCount = 0; @endphp
                                            @foreach ($peminjaman as $index => $item)
                                                @if ($item->user && $item->user->level == 'siswa')
                                                    @php $siswaCount++; @endphp
                                                    <tr>
                                                        <td>{{ $siswaCount }}</td>
                                                        <td>{{ $item->no_peminjaman }}</td>
                                                        <td>{{ $item->buku->judul }}</td>
                                                        <td>{{ $item->user->nama }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($item->tanggal_pengembalian)
                                                                {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->status == 'Diproses')
                                                                <span class="badge"
                                                                    style="color: #0077ff; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dipinjam')
                                                                <span class="badge"
                                                                    style="color: #ffc107; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dikembalikan')
                                                                @if ($item->is_terlambat)
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->jumlah_hari_terlambat }} hari)
                                                                    </span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                @endif
                                                            @elseif ($item->status == 'Terlambat')
                                                                @if ($item->is_late && $item->late_days > 0)
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->late_days }} hari)</span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #ffc107; font-weight: bold">Dipinjam</span>
                                                                @endif
                                                            @elseif ($item->status == 'Dibatalkan')
                                                                <span class="badge"
                                                                    style="color: #dc3545; font-weight: bold">{{ $item->status }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td style="display: none;">{{ $item->catatan ?? '-' }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('peminjaman.detail', $item->id) }}"
                                                                    class="btn btn-sm btn-info" title="Detail">
                                                                    <i class="bx bx-info-circle"></i>
                                                                </a>

                                                                @if ($item->status == 'Diproses')
                                                                    @if (auth()->user()->level == 'admin')
                                                                        @if ($item->diproses_by == 'admin')
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success-pengambilan"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#pengambilanModal"
                                                                                data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                title="Konfirmasi Pengambilan">
                                                                                <i class="bx bx-package"></i>
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        @if ($item->user_id == auth()->id())
                                                                            @if ($item->diproses_by == null)
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-success-pengambilan"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#pengambilanModal"
                                                                                    data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                    title="Konfirmasi Pengambilan">
                                                                                    <i class="bx bx-package"></i>
                                                                                </button>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endif

                                                                @if ($item->status == 'Dipinjam' || $item->status == 'Terlambat')
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success-peminjaman"
                                                                        data-peminjaman-id="{{ $item->id }}"
                                                                        data-judul-buku="{{ $item->buku->judul }}"
                                                                        data-nama-peminjam="{{ $item->user->nama }}"
                                                                        data-tanggal-pinjam="{{ $item->tanggal_pinjam }}"
                                                                        data-tanggal-kembali="{{ $item->tanggal_kembali }}"
                                                                        data-harga-buku="{{ $item->buku->harga_buku ?? 0 }}"
                                                                        title="Konfirmasi Pengembalian">
                                                                        <i class="bx bx-check"></i>
                                                                    </button>
                                                                @endif

                                                                @if (auth()->user()->level == 'admin')
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger delete-btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal"
                                                                        data-action="{{ route('peminjaman.hapus', $item->id) }}"
                                                                        title="Hapus">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!request('user_type') || request('user_type') == 'guru')
                    <div class="tab-pane fade show active" id="guru-peminjaman" role="tabpanel"
                        aria-labelledby="guru-tab">
                        <div class="data">
                            <div class="content-data">
                                <div class="head">
                                    <h3>Daftar Peminjaman Guru</h3>
                                    <div class="menu">
                                        <span style="color: var(--dark-grey); font-size: 14px;">
                                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk
                                            mengunduh data
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive p-3">
                                    <table id="tableGuru" class="table align-items-center table-flush table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>No. Peminjaman</th>
                                                <th>Judul Buku</th>
                                                <th>Nama Peminjam</th>
                                                <th>Tanggal Pinjam</th>
                                                <th>Tanggal Batas Kembali</th>
                                                <th>Tanggal Pengembalian</th>
                                                <th>Status</th>
                                                <th style="display: none;">Catatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $guruCount = 0; @endphp
                                            @foreach ($peminjaman as $index => $item)
                                                @if ($item->user && $item->user->level == 'guru')
                                                    @php $guruCount++; @endphp
                                                    <tr>
                                                        <td>{{ $guruCount }}</td>
                                                        <td>{{ $item->no_peminjaman }}</td>
                                                        <td>{{ $item->buku->judul }}</td>
                                                        <td>{{ $item->user->nama }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($item->tanggal_pengembalian)
                                                                {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->status == 'Diproses')
                                                                <span class="badge"
                                                                    style="color: #0077ff; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dipinjam')
                                                                <span class="badge"
                                                                    style="color: #ffc107; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dikembalikan')
                                                                @if ($item->is_terlambat)
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->jumlah_hari_terlambat }} hari)
                                                                    </span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                @endif
                                                            @elseif ($item->status == 'Terlambat')
                                                                @if ($item->is_late && $item->late_days > 0)
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->late_days }} hari)</span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #ffc107; font-weight: bold">Dipinjam</span>
                                                                @endif
                                                            @elseif ($item->status == 'Dibatalkan')
                                                                <span class="badge"
                                                                    style="color: #dc3545; font-weight: bold">{{ $item->status }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td style="display: none;">{{ $item->catatan ?? '-' }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('peminjaman.detail', $item->id) }}"
                                                                    class="btn btn-sm btn-info" title="Detail">
                                                                    <i class="bx bx-info-circle"></i>
                                                                </a>

                                                                @if ($item->status == 'Diproses')
                                                                    @if (auth()->user()->level == 'admin')
                                                                        @if ($item->diproses_by == 'admin')
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success-pengambilan"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#pengambilanModal"
                                                                                data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                title="Konfirmasi Pengambilan">
                                                                                <i class="bx bx-package"></i>
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        @if ($item->user_id == auth()->id())
                                                                            @if ($item->diproses_by == null)
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-success-pengambilan"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#pengambilanModal"
                                                                                    data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                    title="Konfirmasi Pengambilan">
                                                                                    <i class="bx bx-package"></i>
                                                                                </button>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endif

                                                                @if ($item->status == 'Dipinjam' || $item->status == 'Terlambat')
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success-peminjaman"
                                                                        data-peminjaman-id="{{ $item->id }}"
                                                                        data-judul-buku="{{ $item->buku->judul }}"
                                                                        data-nama-peminjam="{{ $item->user->nama }}"
                                                                        data-tanggal-pinjam="{{ $item->tanggal_pinjam }}"
                                                                        data-tanggal-kembali="{{ $item->tanggal_kembali }}"
                                                                        data-harga-buku="{{ $item->buku->harga_buku ?? 0 }}"
                                                                        title="Konfirmasi Pengembalian">
                                                                        <i class="bx bx-check"></i>
                                                                    </button>
                                                                @endif

                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger delete-btn"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                    data-action="{{ route('peminjaman.hapus', $item->id) }}"
                                                                    title="Hapus">
                                                                    <i class="bx bx-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (!request('user_type') || request('user_type') == 'staff')
                    <div class="tab-pane fade {{ request('user_type') == 'staff' ? 'show active' : '' }}"
                        id="staff-peminjaman" role="tabpanel" aria-labelledby="staff-tab">
                        <div class="data">
                            <div class="content-data">
                                <div class="head">
                                    <h3>Daftar Peminjaman Staff</h3>
                                    <div class="menu">
                                        <span style="color: var(--dark-grey); font-size: 14px;">
                                            <i class='bx bx-info-circle'></i> Gunakan tombol export di bawah tabel untuk
                                            mengunduh data
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive p-3">
                                    <table id="tableStaff" class="table align-items-center table-flush table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>No. Peminjaman</th>
                                                <th>Judul Buku</th>
                                                <th>Nama Peminjam</th>
                                                <th>Tanggal Pinjam</th>
                                                <th>Tanggal Batas Kembali</th>
                                                <th>Tanggal Pengembalian</th>
                                                <th>Status</th>
                                                <th style="display: none;">Catatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $staffCount = 0; @endphp
                                            @foreach ($peminjaman as $index => $item)
                                                @if ($item->user && $item->user->level == 'staff')
                                                    @php $staffCount++; @endphp
                                                    <tr>
                                                        <td>{{ $staffCount }}</td>
                                                        <td>{{ $item->no_peminjaman }}</td>
                                                        <td>{{ $item->buku->judul }}</td>
                                                        <td>{{ $item->user->nama }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($item->tanggal_pengembalian)
                                                                {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($item->status == 'Diproses')
                                                                <span class="badge"
                                                                    style="color: #0077ff; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dipinjam')
                                                                <span class="badge"
                                                                    style="color: #ffc107; font-weight: bold">{{ $item->status }}</span>
                                                            @elseif ($item->status == 'Dikembalikan')
                                                                @if ($item->is_terlambat)
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->jumlah_hari_terlambat }} hari)
                                                                    </span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                                @endif
                                                            @elseif ($item->status == 'Terlambat')
                                                                @if ($item->is_late && $item->late_days > 0)
                                                                    <span class="badge"
                                                                        style="color: #dc3545; font-weight: bold">Terlambat
                                                                        ({{ $item->late_days }} hari)</span>
                                                                @else
                                                                    <span class="badge"
                                                                        style="color: #ffc107; font-weight: bold">Dipinjam</span>
                                                                @endif
                                                            @elseif ($item->status == 'Dibatalkan')
                                                                <span class="badge"
                                                                    style="color: #dc3545; font-weight: bold">{{ $item->status }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td style="display: none;">{{ $item->catatan ?? '-' }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('peminjaman.detail', $item->id) }}"
                                                                    class="btn btn-sm btn-info" title="Detail">
                                                                    <i class="bx bx-info-circle"></i>
                                                                </a>

                                                                @if ($item->status == 'Diproses')
                                                                    @if (auth()->user()->level == 'admin')
                                                                        @if ($item->diproses_by == 'admin')
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success-pengambilan"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#pengambilanModal"
                                                                                data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                title="Konfirmasi Pengambilan">
                                                                                <i class="bx bx-package"></i>
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        @if ($item->user_id == auth()->id())
                                                                            @if ($item->diproses_by == null)
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-success-pengambilan"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#pengambilanModal"
                                                                                    data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                                    title="Konfirmasi Pengambilan">
                                                                                    <i class="bx bx-package"></i>
                                                                                </button>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    @endif @if ($item->status == 'Dipinjam' || $item->status == 'Terlambat')
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-success-peminjaman"
                                                                            data-peminjaman-id="{{ $item->id }}"
                                                                            data-judul-buku="{{ $item->buku->judul }}"
                                                                            data-nama-peminjam="{{ $item->user->nama }}"
                                                                            data-tanggal-pinjam="{{ $item->tanggal_pinjam }}"
                                                                            data-tanggal-kembali="{{ $item->tanggal_kembali }}"
                                                                            data-harga-buku="{{ $item->buku->harga_buku ?? 0 }}"
                                                                            title="Konfirmasi Pengembalian">
                                                                            <i class="bx bx-check"></i>
                                                                        </button>
                                                                    @endif

                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger delete-btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteModal"
                                                                        data-action="{{ route('peminjaman.hapus', $item->id) }}"
                                                                        title="Hapus">
                                                                        <i class="bx bx-trash"></i>
                                                                    </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Filter untuk non-admin -->
            <div class="filter">
                <div class="card">
                    <div class="head">
                        <h3>Filter Rentang Waktu</h3>
                    </div>
                    <form action="{{ route('peminjaman.index') }}" method="GET" class="form-group"
                        style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">

                        {{-- Filter status --}}
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <label for="status">Status:</label>
                            <select name="status" id="status" class="form-control" style="max-width: 180px;">
                                <option value="">Semua Status</option>
                                <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses
                                </option>
                                <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam
                                </option>
                                <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan</option>
                                <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>
                                    Terlambat
                                </option>
                                <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>
                                    Dibatalkan
                                </option>
                            </select>
                        </div>

                        <div style="display: flex; align-items: center; gap: 5px;">
                            <label for="start_date">Dari:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ request('start_date') }}" style="width: 150px;">
                        </div>

                        <div style="display: flex; align-items: center; gap: 5px;">
                            <label for="end_date">Sampai:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ request('end_date') }}" style="width: 150px;">
                        </div>

                        <button type="submit" class="btn-download btn-filter" style="padding: 5px 15px;">
                            <i class='bx bx-filter'></i> Filter
                        </button>

                        @if (request('status') || request('start_date') || request('end_date'))
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary"
                                style="padding: 5px 15px;">
                                <i class='bx bx-reset'></i> Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Tampilan untuk user non-admin -->
            <div class="data">
                <div class="content-data">
                    <div class="head">
                        <h3>Daftar Peminjaman Anda</h3>
                    </div>
                    <div class="table-responsive p-3">
                        <table id="dataTable" class="table align-items-center table-flush table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No.</th>
                                    <th>No. Peminjaman</th>
                                    <th>Judul Buku</th>
                                    {{-- <th>Nama Peminjam</th> --}}
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Batas Kembali</th>
                                    <th>Tanggal Pengembalian</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 0; @endphp
                                @foreach ($peminjaman as $index => $item)
                                    @if ($item->user_id == auth()->user()->id)
                                        @php $count++; @endphp
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>{{ $item->no_peminjaman }}</td>
                                            <td>{{ $item->buku->judul }}</td>
                                            {{-- <td>{{ $item->user->nama }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($item->tanggal_pengembalian)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->status == 'Diproses')
                                                    <span class="badge"
                                                        style="color: #0077ff; font-weight: bold">{{ $item->status }}</span>
                                                @elseif ($item->status == 'Dipinjam')
                                                    <span class="badge"
                                                        style="color: #ffc107; font-weight: bold">{{ $item->status }}</span>
                                                @elseif ($item->status == 'Dikembalikan')
                                                    @if ($item->is_terlambat)
                                                        <span class="badge"
                                                            style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                        <span class="badge"
                                                            style="color: #dc3545; font-weight: bold">Terlambat
                                                            ({{ $item->jumlah_hari_terlambat }} hari)
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="color: #28a745; font-weight: bold">{{ $item->status }}</span>
                                                    @endif
                                                @elseif ($item->status == 'Terlambat')
                                                    @if ($item->is_late && $item->late_days > 0)
                                                        <span class="badge"
                                                            style="color: #dc3545; font-weight: bold">Terlambat
                                                            ({{ $item->late_days }})</span>
                                                    @else
                                                        <span class="badge"
                                                            style="color: #ffc107; font-weight: bold">Dipinjam</span>
                                                    @endif
                                                @elseif ($item->status == 'Dibatalkan')
                                                    <span class="badge"
                                                        style="color: #dc3545; font-weight: bold">{{ $item->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('peminjaman.detail', $item->id) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="bx bx-info-circle"></i>
                                                    </a>

                                                    @if ($item->status == 'Diproses')
                                                        @if ($item->diproses_by == 'admin' || $item->diproses_by == null)
                                                            <button type="button"
                                                                class="btn btn-sm btn-success-pengambilan"
                                                                data-bs-toggle="modal" data-bs-target="#pengambilanModal"
                                                                data-action="{{ route('peminjaman.konfirmasi-pengambilan', $item->id) }}"
                                                                title="Konfirmasi Pengambilan">
                                                                <i class="bx bx-package"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Include Modal Sanksi -->
    @include('components.modal-sanksi')

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade bootstrap-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data peminjaman ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pengambilan -->
    <div class="modal fade bootstrap-modal" id="pengambilanModal" tabindex="-1" aria-labelledby="pengambilanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pengambilanModalLabel">Konfirmasi Pengambilan Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tanggal pinjam akan diatur ke tanggal hari ini.</p>
                    <p>Apakah Anda yakin ingin mengkonfirmasi pengambilan buku ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="pengambilan-form" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Konfirmasi Pengambilan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- File JavaScript peminjaman -->
    <script src="{{ asset('assets/js/peminjaman/peminjaman.js') }}"></script>

    <!-- Script sederhana untuk mobile -->
    <script>
        // Mobile touch enhancement
        if (window.innerWidth <= 768) {
            document.addEventListener('DOMContentLoaded', function() {
                // Re-attach handlers setelah DataTable dimuat
                setTimeout(function() {
                    document.querySelectorAll('.btn-success-peminjaman').forEach(function(btn) {
                        btn.style.touchAction = 'manipulation';
                        btn.style.minHeight = '44px';
                        btn.style.minWidth = '44px';
                    });
                }, 2000);
            });
        }
    </script>
@endsection
