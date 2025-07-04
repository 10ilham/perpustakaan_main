@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Edit Staff Profile</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item"><a href="{{ route('staff.profile') }}">Profile</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="profile-card">
                <div class="card-body">
                    <form action="{{ route('staff.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                value="{{ $staff->user->nama }}" required>
                            @error('nama')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ $staff->user->email }}" required>
                            @error('email')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" name="nip" id="nip" class="form-control"
                                value="{{ $staff->nip }}" required>
                            @error('nip')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="bagian">Bagian</label>
                            <select name="bagian" id="bagian" class="form-control" required>
                                <option value="" disabled selected>Pilih Bagian</option>
                                <option value="Kepala Tata Usaha" {{ $staff->bagian == 'Kepala Tata Usaha' ? 'selected' : '' }}>Kepala Tata Usaha</option>
                                <option value="Tata Usaha" {{ $staff->bagian == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                <option value="Staff Keuangan" {{ $staff->bagian == 'Staff Keuangan' ? 'selected' : '' }}>Staff Keuangan</option>
                                <option value="Staff Laboratorium" {{ $staff->bagian == 'Staff Laboratorium' ? 'selected' : '' }}>Staff Laboratorium</option>
                                <option value="Staff Keamanan" {{ $staff->bagian == 'Staff Keamanan' ? 'selected' : '' }}>Staff Keamanan</option>
                                <option value="Staff Kebersihan" {{ $staff->bagian == 'Staff Kebersihan' ? 'selected' : '' }}>Staff Kebersihan</option>
                            </select>
                            @error('bagian')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                value="{{ $staff->tanggal_lahir }}" required>
                            @error('tanggal_lahir')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ $staff->alamat }}</textarea>
                            @error('alamat')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="no_telepon">Nomor Telepon</label>
                            <input type="text" name="no_telepon" id="no_telepon" class="form-control"
                                value="{{ $staff->no_telepon }}" required>
                            @error('no_telepon')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            @error('password')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Konfirmasi password">
                        </div>
                        <!-- Upload Foto dengan review -->
                        <div class="form-group">
                            <label for="foto">Foto Profil</label>
                            <input type="file" name="foto" id="foto" class="form-control"
                                onchange="previewImage(event)" value="{{ $staff->foto }}">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto profil.</small>

                            <!-- Preview Foto -->
                            <div class="mt-3" id="preview-container" style="display: none;">
                                <label for="preview" style="margin-top: 15px;">Preview Foto:</label>
                                <div class="preview-box"
                                    style="border: 2px solid #ddd; padding: 5px; display: inline-block; border-radius: 5px; margin-top: 5px;">
                                    <img id="preview" src="{{ asset('assets/img/staff_foto/' . $staff->foto) }}"
                                        alt="Preview Foto"
                                        style="max-width: 120px; max-height: 120px; border-radius: 3px;">
                                </div>
                            </div>

                            @error('foto')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: {{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group text-end">
                            <a href="{{ route('staff.profile') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
