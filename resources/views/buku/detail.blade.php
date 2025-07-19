@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h1 class="title">Detail Buku</h1>
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
                            <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Sampul Buku -->
                <div class="col-12 col-lg-4">
                    <div class="profile-card">
                        <div class="card-body text-center">
                            <!-- Bar atas dengan kode buku dan stok buku -->
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; width: 100%;">
                                <div class="kode-buku"
                                    style="background: rgba(0, 0, 0, 0.7); color: white; padding: 5px 8px; border-radius: 5px; display: flex; align-items: center; font-size: 14px; flex: 1; margin-right: 10px;">
                                    Kode Buku: {{ $buku->kode_buku }}
                                </div>
                                <div class="stok-buku"
                                    style="background: rgba(185, 165, 9, 0.7); color: white; padding: 5px 8px; border-radius: 5px; font-size: 14px; display: flex; align-items: center; justify-content: center; width: auto; white-space: nowrap;">
                                    Stok: {{ $buku->stok_buku }}
                                </div>
                            </div>

                            <div class="book-cover">
                                @if ($buku->foto)
                                    <img src="{{ asset('assets/img/buku/' . $buku->foto) }}" alt="{{ $buku->judul }}"
                                        style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                @else
                                    <img src="{{ asset('assets/img/default_buku.png') }}" alt="Default Book Cover"
                                        style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                @endif
                            </div>
                            <h3 class="mt-4">{{ $buku->judul }}</h3>
                            <p class="text-muted mb-2">{{ $buku->pengarang }}</p>

                            <!-- Status Buku -->
                            <div class="status-badge-center">
                                @if ($buku->status === 'Tersedia')
                                    <span
                                        class="badge badge-outline-success status-badge-custom">{{ $buku->status }}</span>
                                @elseif($buku->status === 'Habis')
                                    <span class="badge badge-outline-danger status-badge-custom">{{ $buku->status }}</span>
                                @else
                                    <span
                                        class="badge badge-outline-warning status-badge-custom">{{ $buku->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- QR Code untuk Peminjaman -->
                    {{-- Menggunakan library simple-qrcode dan extension imagick (untuk membaca qr code) --}}
                    @if (auth()->user()->level == 'siswa' || auth()->user()->level == 'guru' || auth()->user()->level == 'staff')
                        <div class="profile-card qr-code-section mt-4">
                            <h5 style="text-align: center; margin-bottom: 10px;">Scan untuk Peminjaman</h5>
                            <div class="qr-code-container mt-2"
                                style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
                                <img
                                    src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(150)->merge(public_path('assets/img/logo_mts.png'), 0.4, true)->errorCorrection('H')->generate(route('peminjaman.form', $buku->id))) }}">
                                <a href="{{ route('buku.qrcode.download', $buku->id) }}" class="btn">
                                    <i class="fas fa-download" style="margin-top: 10px"></i> Download QR
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detail Informasi Buku -->
                <div class="col-12 col-lg-8">
                    <div class="profile-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4" style="margin-bottom: 10px">Informasi Buku</h4>

                            <form class="profile-display">
                                {{-- Total Keseluruhan Buku --}}
                                {{-- <div class="form-group">
                                    <label for="total_buku">Total Buku</label>
                                    <input type="text" id="total_buku" class="form-control" value="{{ $totalStokBuku }}"
                                        readonly>
                                </div> --}}

                                {{-- <div class="form-group">
                                    <label for="kategori">Kategori</label>
                                    <input type="text" id="kategori" class="form-control"
                                        value="{{ $buku->kategori->pluck('nama_kategori')->implode(', ') }}" readonly>
                                </div> --}}

                                {{-- <div class="form-group">
                                    <label for="penerbit">Penerbit</label>
                                    <input type="text" id="penerbit" class="form-control" value="{{ $buku->penerbit }}"
                                        readonly>
                                </div> --}}

                                {{-- <div class="form-group">
                                    <label for="tahun_terbit">Tahun Terbit</label>
                                    <input type="text" id="tahun_terbit" class="form-control"
                                        value="{{ $buku->tahun_terbit }}" readonly>
                                </div> --}}

                                @if (auth()->user()->level == 'admin')
                                <div class="form-group">
                                    <label for="harga_buku">Harga Buku</label>
                                    <input type="text" id="harga_buku" class="form-control"
                                        value="Rp {{ number_format($buku->harga_buku ?? 0, 0, ',', '.') }}" readonly>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label for="deskripsi">Sinopsis</label>
                                    <textarea id="deskripsi" class="form-control" rows="5" readonly>{{ $buku->deskripsi }}</textarea>
                                </div>

                                {{-- <div class="form-group">
                                    <label for="created_at">Ditambahkan Pada</label>
                                    <input type="text" id="created_at" class="form-control"
                                        value="{{ $buku->created_at->format('d F Y H:i') }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" id="updated_at" class="form-control"
                                        value="{{ $buku->updated_at->format('d F Y H:i') }}" readonly>
                                </div> --}}

                                <!-- Tombol Aksi -->
                                <div class="form-group text-end">
                                    {{-- Parameter referensi yang akan digunakan di controller --}}
                                    @if (isset($ref) && $ref == 'kategori' && isset($kategori_id))
                                        <a href="{{ route('kategori.detail', ['id' => $kategori_id, 'page' => $page ?? '', 'search' => $search ?? '']) }}"
                                            class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali ke Kategori
                                        </a>
                                    @elseif (isset($dashboard) && $dashboard == 'admin')
                                        {{-- 'admin' penamaan url dari file dashboard admin --}}
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali ke Dashboard
                                        </a>
                                    @elseif (isset($dashboard) && $dashboard == 'anggota')
                                        {{-- 'anggota' penamaan url dari file dashboard anggota --}}
                                        <a href="{{ route('anggota.dashboard') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali ke Dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('buku.index', ['page' => $page ?? '', 'search' => $search ?? '', 'kategori' => $kategoriFilter ?? '', 'status' => $status ?? '']) }}"
                                            class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/buku/buku.js') }}"></script>
@endsection
