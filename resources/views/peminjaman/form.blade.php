@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Form Peminjaman Buku</h1>
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
                            <li class="breadcrumb-item active" aria-current="page">Form Peminjaman</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Detail Buku yang Dipinjam -->
                <div class="col-12 col-md-4">
                    <div class="profile-card">
                        <div class="card-body">
                            <h4 class="card-title">Detail Buku</h4>
                            <div class="row buku-container single-card">
                                <div class="col-12">
                                    <div class="card card-buku text-center align-items-center justify-content-center">
                                        @if ($buku->foto)
                                            <img class="card-img-top" style="max-height: 180px;"
                                                src="{{ asset('assets/img/buku/' . $buku->foto) }}"
                                                alt="{{ $buku->judul }}">
                                        @else
                                            <img class="card-img-top" style="height: 200px;"
                                                src="{{ asset('assets/img/default_buku.png') }}" alt="Default Book Cover">
                                        @endif
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            <div class="detail-buku" style="width: 100%;">
                                                <h5 class="card-title text-center">
                                                    {{ $buku->judul }}
                                                </h5>
                                                <p class="card-text m-0">Kode Buku:
                                                    {{ $buku->kode_buku }}</p>
                                                <p class="card-text m-0">Pengarang:
                                                    {{ $buku->pengarang }}</p>
                                                <p class="card-text m-0">Kategori:
                                                    {{ $buku->kategori->pluck('nama_kategori')->implode(', ') }}</p>
                                                <p class="card-text m-0">Penerbit:
                                                    {{ $buku->penerbit }}</p>
                                                <p class="card-text m-0">Tahun Terbit:
                                                    {{ $buku->tahun_terbit }}</p>
                                                <p class="card-text m-0">Stok:
                                                    {{ $buku->stok_buku }}</p>
                                                <p class="card-text m-0">Status:
                                                    @if ($buku->status === 'Tersedia')
                                                        <span style="color: green;">{{ $buku->status }}</span>
                                                    @elseif ($buku->status === 'Habis')
                                                        <span style="color: red;">{{ $buku->status }}</span>
                                                    @else
                                                        <span style="color: orange;">{{ $buku->status }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Peminjaman -->
                <div class="col-12 col-md-8">
                    <div class="profile-card">
                        <div class="card-body">
                            <h4 class="card-title">Form Peminjaman</h4>

                            <form action="{{ route('peminjaman.pinjam') }}" method="POST">
                                @csrf
                                <input type="hidden" name="buku_id" value="{{ $buku->id }}">

                                <div class="form-group mb-3">
                                    <label for="nama">Nama Peminjam</label>
                                    <input type="text" name="nama" id="nama" class="form-control"
                                        placeholder="Masukkan nama Anda sebagai peminjam"
                                        value="{{ auth()->user()->nama }}" readonly>
                                    <small class="form-text" style="color: #6c757d; opacity: 0.7;">
                                        *Nama peminjam otomatis terisi sesuai akun Anda.
                                    </small>
                                    @error('nama')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                                                class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                                required>
                                            @error('tanggal_pinjam')
                                                <div class="custom-alert" role="alert">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                        </path>
                                                    </svg>
                                                    <p>{{ $message }}</p>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_kembali">Tanggal Kembali</label>
                                            <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                                class="form-control" min="{{ date('Y-m-d') }}" placeholder="yyyy-mm-dd">
                                            <small class="form-text" style="color: #6c757d; opacity: 0.7;">
                                                *Tanggal pengembalian maksimal 3 hari dari tanggal pinjam.
                                            </small>
                                            @error('tanggal_kembali')
                                                <div class="custom-alert" role="alert">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                        </path>
                                                    </svg>
                                                    <p>{{ $message }}</p>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="catatan">Catatan (Opsional)</label>
                                    <textarea name="catatan" id="catatan" class="form-control" placeholder="Masukkan catatan Anda di sini (opsional)"
                                        rows="3"></textarea>
                                    @error('catatan')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group text-end">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('buku.index') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bx bx-book"></i> Pinjam Buku
                                        </button>
                                    </div>
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
    <!-- File JavaScript peminjaman -->
    <script src="{{ asset('assets/js/peminjaman/peminjaman.js') }}"></script>
@endsection
