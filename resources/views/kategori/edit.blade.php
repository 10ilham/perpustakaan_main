@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Edit Kategori</h1>
                        <ol class="breadcrumb">
                            @if (auth()->user()->level == 'admin')
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @endif
                            <li class="divider">/</li>
                            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="profile-card">
                <div class="card-body">
                    <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
                        @csrf
                        {{-- Parameter referensi yang akan digunakan di controller --}}
                        @if (isset($ref))
                            <input type="hidden" name="ref" value="{{ $ref }}">
                        @endif

                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control"
                                value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                            @error('nama_kategori')
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

                        <div class="form-group mt-3">
                            <label for="deskripsi">Deskripsi Kategori</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                            <small class="text-muted">Deskripsi kategori bersifat opsional</small>
                            @error('deskripsi')
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

                        <div class="form-group mt-4 text-end">
                            {{-- Parameter referensi yang akan digunakan di controller --}}
                            @if (isset($ref) && $ref == 'detail')
                                <a href="{{ route('kategori.detail', $kategori->id) }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back"></i> Kembali ke Detail
                                </a>
                            @else
                                <a href="{{ route('kategori.index') }}" class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back"></i> Kembali
                                </a>
                            @endif
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
