@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Form Peminjaman Manual</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Peminjaman</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Peminjaman Manual</li>
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
                                        <img id="bukuImage" class="card-img-top" style="max-height: 180px; display: none;"
                                            src="" alt="Book Cover">
                                        <img id="defaultImage" class="card-img-top" style="height: 200px;"
                                            src="{{ asset('assets/img/default_buku.png') }}" alt="Default Book Cover">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            <div class="detail-buku" style="width: 100%;">
                                                <div id="bukuInfo">
                                                    <h5 class="card-title text-center text-muted">
                                                        Pilih buku untuk melihat detail
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Peminjaman Manual -->
                <div class="col-12 col-md-8">
                    <div class="profile-card">
                        <div class="card-body">
                            <h4 class="card-title">Form Peminjaman Manual</h4>

                            <form method="POST" action="{{ route('peminjaman.manual.simpan') }}" id="manualPeminjamanForm">
                                @csrf

                                <!-- Pilih Buku -->
                                <div class="form-group mb-3">
                                    <label for="buku_id">Pilih Buku <span class="text-danger">*</span></label>
                                    <!-- Search Input dengan style yang sama seperti index buku -->
                                    <div class="navbar-search mb-2" style="max-width: 400px;">
                                        <div class="input-group">
                                            <input type="text" id="searchBuku"
                                                class="form-control bg-light border-1 small"
                                                placeholder="Cari judul buku..." aria-label="Search"
                                                aria-describedby="basic-addon2" autocomplete="off"
                                                style="border-color: #244fbc;">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="clearSearch"
                                                    title="Hapus pencarian">
                                                    <i class="bx bx-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <select name="buku_id" id="buku_id"
                                        class="form-control @error('buku_id') is-invalid @enderror" required
                                        style="width: 100%;">
                                        <option value="">-- Pilih Buku --</option>
                                        @foreach ($buku as $item)
                                            <option value="{{ $item->id }}" data-judul="{{ $item->judul }}"
                                                data-pengarang="{{ $item->pengarang }}"
                                                data-penerbit="{{ $item->penerbit }}" data-stok="{{ $item->stok_buku }}"
                                                data-kategori="{{ $item->kategori->pluck('nama_kategori')->implode(', ') }}"
                                                data-foto="{{ $item->foto }}" data-kode-buku="{{ $item->kode_buku }}"
                                                data-tahun-terbit="{{ $item->tahun_terbit }}"
                                                data-status="{{ $item->status }}"
                                                {{ old('buku_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->judul }} - {{ $item->pengarang }} (Stok:
                                                {{ $item->stok_buku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('buku_id')
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
                                    <!-- Level Anggota -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="user_level">Level Anggota <span class="text-danger">*</span></label>
                                            <select name="user_level" id="user_level"
                                                class="form-control @error('user_level') is-invalid @enderror" required>
                                                <option value="">-- Pilih Level --</option>
                                                <option value="siswa"
                                                    {{ old('user_level') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                                <option value="guru" {{ old('user_level') == 'guru' ? 'selected' : '' }}>
                                                    Guru</option>
                                                <option value="staff"
                                                    {{ old('user_level') == 'staff' ? 'selected' : '' }}>Staff</option>
                                            </select>
                                            @error('user_level')
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

                                    <!-- Pilih Anggota -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="user_id">Pilih Anggota <span class="text-danger">*</span></label>
                                            <select name="user_id" id="user_id"
                                                class="form-control @error('user_id') is-invalid @enderror" required
                                                disabled>
                                                <option value="">-- Pilih level terlebih dahulu --</option>
                                            </select>
                                            @error('user_id')
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

                                <div class="row">
                                    <!-- Tanggal Pinjam -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_pinjam">Tanggal Pinjam</label>
                                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                                                class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                                                value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                                                min="{{ date('Y-m-d') }}" required>
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

                                    <!-- Tanggal Kembali -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="tanggal_kembali">Tanggal Kembali</label>
                                            <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                                                class="form-control @error('tanggal_kembali') is-invalid @enderror"
                                                value="{{ old('tanggal_kembali') }}" min="{{ date('Y-m-d') }}"
                                                placeholder="yyyy-mm-dd" required>
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

                                <!-- Catatan -->
                                <div class="form-group mb-3">
                                    <label for="catatan">Catatan (Opsional)</label>
                                    <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3"
                                        maxlength="500" placeholder="Masukkan catatan Anda di sini (opsional)">{{ old('catatan') }}</textarea>
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
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="bx bx-book"></i> Simpan Peminjaman
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

    <!-- Loading Overlay -->
    <div id="loadingOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p>Memuat data anggota...</p>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- File JavaScript peminjaman -->
    <script src="{{ asset('assets/js/peminjaman/peminjaman.js') }}"></script>

    <script>
        // Set data untuk compatibility dengan fungsi JS eksternal
        window.oldUserId = "{{ old('user_id') }}";
    </script>
@endsection
