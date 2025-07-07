@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Detail Peminjaman Buku</h1>
                        <ol class="breadcrumb">
                            @if (auth()->user()->level == 'admin')
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @elseif(auth()->user()->level == 'siswa')
                                <li class="breadcrumb-item"><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                            @elseif(auth()->user()->level == 'guru')
                                <li class="breadcrumb-item"><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                            @elseif(auth()->user()->level == 'staff')
                                <li class="breadcrumb-item"><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                            @endif
                            <li class="divider">/</li>
                            <li><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Detail Buku yang Dipinjam - Kolom Kiri -->
                <div class="col-12 col-md-4">
                    <div class="profile-card">
                        <div class="card-body">
                            <h4 class="card-title">Detail Buku</h4>
                            <div class="row buku-container single-card">
                                <div class="col-12">
                                    <div class="card card-buku text-center align-items-center justify-content-center">
                                        @if ($peminjaman->buku->foto)
                                            <img class="card-img-top" style="max-height: 180px;"
                                                src="{{ asset('assets/img/buku/' . $peminjaman->buku->foto) }}"
                                                alt="{{ $peminjaman->buku->judul }}">
                                        @else
                                            <img class="card-img-top" style="height: 200px;"
                                                src="{{ asset('assets/img/default_buku.png') }}" alt="Default Book Cover">
                                        @endif
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            <div class="detail-buku" style="width: 100%;">
                                                <h5 class="card-title text-center">
                                                    {{ $peminjaman->buku->judul }}
                                                </h5>
                                                <p class="card-text m-0">Kode Buku:
                                                    {{ $peminjaman->buku->kode_buku }}</p>
                                                <p class="card-text m-0">Pengarang:
                                                    {{ $peminjaman->buku->pengarang }}</p>
                                                <p class="card-text m-0">Kategori:
                                                    {{ $peminjaman->buku->kategori->pluck('nama_kategori')->implode(', ') }}
                                                </p>
                                                <p class="card-text m-0">Penerbit:
                                                    {{ $peminjaman->buku->penerbit }}</p>
                                                <p class="card-text m-0">Tahun Terbit:
                                                    {{ $peminjaman->buku->tahun_terbit }}</p>
                                                <p class="card-text m-0">Harga Buku:
                                                    <span class="text-primary">Rp
                                                        {{ number_format($peminjaman->buku->harga_buku, 0, ',', '.') }}</span>
                                                </p>
                                                <p class="card-text m-0">Stok:
                                                    {{ $peminjaman->buku->stok_buku }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Peminjaman - Kolom Kanan -->
                <div class="col-12 col-md-8">
                    <div class="profile-card">
                        <div class="card-body" style="display: flex; flex-direction: column; height: 100%;">
                            <h4 class="card-title">Informasi Peminjaman</h4>

                            <!-- Status Peminjaman -->
                            <div class="status-badge mb-4">
                                @if (
                                    ($peminjaman->status == 'Dipinjam' || $peminjaman->status == 'Terlambat') &&
                                        $peminjaman->is_late &&
                                        $peminjaman->late_days > 0)
                                    <div class="status-box status-late">
                                        <div class="icon">
                                            <i class="bx bx-error-circle"></i>
                                        </div>
                                        <div class="info">
                                            <h4>Terlambat ({{ $peminjaman->late_days }} hari)</h4>
                                            <p>Buku belum dikembalikan dan sudah melewati batas waktu.</p>
                                        </div>
                                    </div>
                                @elseif ($peminjaman->status == 'Dipinjam' || ($peminjaman->status == 'Terlambat' && !$peminjaman->is_late))
                                    <div class="status-box status-borrowed">
                                        <div class="icon">
                                            <i class="bx bx-time"></i>
                                        </div>
                                        <div class="info">
                                            <h4>Dipinjam</h4>
                                            <p>Buku sedang dipinjam.</p>
                                        </div>
                                    </div>
                                @elseif ($peminjaman->status == 'Dikembalikan')
                                    <div class="status-box status-returned">
                                        <div class="icon">
                                            <i class="bx bx-check-circle"></i>
                                        </div>
                                        <div class="info">
                                            @if ($peminjaman->is_terlambat)
                                                <h4>{{ $peminjaman->status }} (Terlambat
                                                    {{ $peminjaman->jumlah_hari_terlambat ?? '0' }} hari)</h4>
                                                <p>Buku telah dikembalikan pada
                                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') }}
                                                    dengan status terlambat.
                                                </p>
                                            @else
                                                <h4>{{ $peminjaman->status }}</h4>
                                                <p>Buku telah dikembalikan pada
                                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') }}.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($peminjaman->status == 'Diproses')
                                    <div class="status-box status-diproses">
                                        <div class="icon">
                                            <i class="bx bx-loader-circle"></i>
                                        </div>
                                        <div class="info">
                                            <h4>{{ $peminjaman->status }}</h4>
                                            <p>Peminjaman sedang diproses. Silakan ambil buku dan konfirmasi pengambilan
                                                buku.</p>
                                        </div>
                                    </div>
                                @elseif ($peminjaman->status == 'Dibatalkan')
                                    <div class="status-box status-dibatalkan">
                                        <div class="icon">
                                            <i class="bx bx-x-circle"></i>
                                        </div>
                                        <div class="info">
                                            <h4>{{ $peminjaman->status }}</h4>
                                            <p>Peminjaman dibatalkan karena buku tidak diambil sebelum tanggal batas
                                                pengembalian kadaluarsa.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="peminjaman-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="label">No. Peminjaman</span>
                                            <span class="value">{{ $peminjaman->no_peminjaman }}</span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="label">Nama Peminjam</span>
                                            <span class="value">{{ $peminjaman->user->nama }}</span>
                                        </div>

                                        @if (auth()->user()->level == 'admin' || auth()->user()->level == 'staff')
                                            <div class="detail-item">
                                                <span class="label">Level Anggota</span>
                                                <span class="value">{{ ucfirst($peminjaman->user->level) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="label">Tanggal Peminjaman</span>
                                            <span
                                                class="value">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</span>
                                        </div>

                                        <div class="detail-item">
                                            <span class="label">Batas Waktu Pengembalian</span>
                                            <span
                                                class="value">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}</span>
                                        </div>

                                        @if ($peminjaman->tanggal_pengembalian)
                                            <div class="detail-item">
                                                <span class="label">Tanggal Pengembalian</span>
                                                <span
                                                    class="value">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->translatedFormat('d F Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($peminjaman->catatan)
                                    <div class="detail-item catatan-item">
                                        <span class="label">Catatan</span>
                                        <div class="catatan text-justify">
                                            {{ $peminjaman->catatan }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Informasi Ketentuan Denda -->
                            <div class="penalty-info mt-4">
                                <h5 style="font-size: 16px"><i class='bx bx-info-circle'></i> Ketentuan Denda Peminjaman
                                </h5>
                                <div class="penalty-rules">
                                    <div class="rule-item">
                                        <strong>Tidak Terlambat + Tidak Rusak:</strong> Tidak ada denda
                                    </div>
                                    <div class="rule-item">
                                        <strong>Tidak Terlambat + Rusak/Hilang:</strong> Denda sesuai harga buku
                                    </div>
                                    <div class="rule-item">
                                        <strong>Terlambat + Tidak Rusak:</strong> Denda Rp 1.000/hari keterlambatan
                                    </div>
                                    <div class="rule-item">
                                        <strong>Terlambat + Rusak/Hilang:</strong> Denda sesuai harga buku (tanpa denda
                                        keterlambatan)
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Sanksi -->
                            @if ($peminjaman->sanksi)
                                <div class="sanksi-info mt-4" style="margin-top: 20px">
                                    <h5 style="font-size: 16px"><i class='bx bx-receipt'></i> Informasi Sanksi
                                    </h5>
                                    @foreach ([$peminjaman->sanksi] as $sanksi)
                                        <div class="sanksi-card">
                                            <div class="sanksi-header">
                                                <div class="sanksi-title">
                                                    <strong>Sanksi :</strong>
                                                    <span
                                                        class="sanksi-date">{{ \Carbon\Carbon::parse($sanksi->created_at)->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="sanksi-status">
                                                    @if ($sanksi->status_bayar == 'sudah_bayar')
                                                        <span class="badge badge-success">Sudah Bayar</span>
                                                    @else
                                                        <span class="badge badge-danger">Belum Bayar</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="sanksi-details">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="sanksi-item">
                                                            <span class="sanksi-label">Jenis Sanksi:</span>
                                                            <div class="sanksi-value">
                                                                @php
                                                                    $jenisSanksi = explode(',', $sanksi->jenis_sanksi);
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
                                                            </div>
                                                        </div>

                                                        @if ($sanksi->hari_terlambat > 0)
                                                            <div class="sanksi-item">
                                                                <span class="sanksi-label">Hari Terlambat:</span>
                                                                <span class="sanksi-value">{{ $sanksi->hari_terlambat }}
                                                                    hari</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="sanksi-item">
                                                            <span class="sanksi-label">Denda Keterlambatan:</span>
                                                            <span class="sanksi-value">
                                                                @if ($sanksi->denda_keterlambatan > 0)
                                                                    <span class="text-warning">Rp
                                                                        {{ number_format($sanksi->denda_keterlambatan, 0, ',', '.') }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </span>
                                                        </div>

                                                        <div class="sanksi-item">
                                                            <span class="sanksi-label">Denda Kerusakan:</span>
                                                            <span class="sanksi-value">
                                                                @if ($sanksi->denda_kerusakan > 0)
                                                                    <span class="text-danger">Rp
                                                                        {{ number_format($sanksi->denda_kerusakan, 0, ',', '.') }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </span>
                                                        </div>

                                                        <div class="sanksi-item">
                                                            <span class="sanksi-label">Total Denda:</span>
                                                            <span class="sanksi-value">
                                                                <strong class="text-primary">Rp
                                                                    {{ number_format($sanksi->total_denda, 0, ',', '.') }}</strong>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($sanksi->keterangan)
                                                    <div class="sanksi-item sanksi-item-keterangan">
                                                        <span class="sanksi-label">Keterangan:</span>
                                                        <div class="sanksi-keterangan">{{ $sanksi->keterangan }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="form-group text-end" style="margin-top: auto;">
                                <div class="d-flex justify-content-between" style="margin-top: 10px">
                                    @if (isset($ref) && $ref == 'anggota' && isset($anggota_id))
                                        <a href="{{ route('anggota.detail', $anggota_id) }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    @elseif (isset($ref) && $ref == 'laporan_belum_kembali')
                                        <a href="{{ route('laporan.belum_kembali') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali ke Laporan
                                        </a>
                                    @elseif (isset($ref) && $ref == 'laporan_sudah_kembali')
                                        <a href="{{ route('laporan.sudah_kembali') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali ke Laporan
                                        </a>
                                    @else
                                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    @endif

                                    @if (auth()->user()->level == 'admin' && $showReturnButton)
                                        <button type="button" class="btn btn-success sanksi-btn"
                                            id="btnKonfirmasiPengembalian">
                                            <i class="bx bx-check"></i> Konfirmasi Pengembalian
                                        </button>
                                    @endif

                                    @if ($peminjaman->status == 'Diproses')
                                        @if (auth()->user()->level == 'admin' && $peminjaman->diproses_by == 'admin')
                                            <button type="button" class="btn btn-success btn-success-pengambilan"
                                                data-bs-toggle="modal" data-bs-target="#pengambilanModal"
                                                data-action="{{ route('peminjaman.konfirmasi-pengambilan', $peminjaman->id) }}"
                                                title="Konfirmasi Pengambilan">
                                                <i class="bx bx-check"></i> Konfirmasi Pengambilan
                                            </button>
                                            {{-- untuk user selain admin --}}
                                        @elseif (auth()->user()->level != 'admin')
                                            @if ($peminjaman->user_id == auth()->id() && ($peminjaman->diproses_by == 'admin' || $peminjaman->diproses_by == null))
                                                <button type="button" class="btn btn-success btn-success-pengambilan"
                                                    data-bs-toggle="modal" data-bs-target="#pengambilanModal"
                                                    data-action="{{ route('peminjaman.konfirmasi-pengambilan', $peminjaman->id) }}"
                                                    title="Konfirmasi Pengambilan">
                                                    <i class="bx bx-check"></i> Konfirmasi Pengambilan
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Konfirmasi Pengambilan -->
    <div class="modal fade bootstrap-modal" id="pengambilanModal" aria-labelledby="pengambilanModalLabel"
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

    <!-- Include Modal Sanksi -->
    @include('components.modal-sanksi')
@endsection

@section('scripts')
    <!-- File JavaScript peminjaman -->
    <script src="{{ asset('assets/js/peminjaman/peminjaman.js') }}"></script>

    <script>
        // Data peminjaman untuk modal sanksi
        window.peminjamanDetailData = {
            peminjaman_id: {{ $peminjaman->id }},
            judul_buku: '{{ $peminjaman->buku->judul }}',
            nama_peminjam: '{{ $peminjaman->user->nama }}',
            tanggal_pinjam: '{{ $peminjaman->tanggal_pinjam }}',
            tanggal_kembali: '{{ $peminjaman->tanggal_kembali }}',
            harga_buku: {{ $peminjaman->buku->harga_buku ?? 0 }}
        };
    </script>
@endsection
