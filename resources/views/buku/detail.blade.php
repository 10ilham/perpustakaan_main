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

                            <!-- Harga Buku (hanya untuk admin) -->
                            @if (auth()->user()->level == 'admin')
                                <div class="mt-3 p-3"
                                    style="background: rgba(52, 152, 219, 0.1); border-radius: 8px; border-left: 4px solid #3498db;">
                                    <div class="text-center">
                                        <h6 class="mb-1" style="color: #2c3e50; font-weight: 600;">Harga Buku</h6>
                                        <h5 class="mb-0" style="color: #27ae60; font-weight: bold;">
                                            Rp {{ number_format($buku->harga_buku ?? 0, 0, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                            @endif
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
                                <div class="form-group">
                                    <label for="deskripsi">Sinopsis</label>
                                    <textarea id="deskripsi" class="form-control" readonly
                                        style="min-height: 200px; resize: none; overflow: hidden; line-height: 1.6; padding: 15px; font-size: 14px;">{{ $buku->deskripsi }}</textarea>
                                </div>

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
    <script>
        // Auto-resize textarea untuk sinopsis
        document.addEventListener('DOMContentLoaded', function() {
            const deskripsiTextarea = document.getElementById('deskripsi');
            if (deskripsiTextarea) {
                // Reset height to auto untuk menghitung scroll height yang benar
                deskripsiTextarea.style.height = 'auto';
                // Set height berdasarkan scroll height dengan minimum 200px untuk memberikan ruang lebih
                const scrollHeight = deskripsiTextarea.scrollHeight;
                const minHeight = 200;
                deskripsiTextarea.style.height = Math.max(scrollHeight + 20, minHeight) + 'px';
            }
        });
    </script>
@endsection
