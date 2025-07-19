@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Tambah Anggota Baru</h1>
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
                            <li class="breadcrumb-item"><a href="{{ route('anggota.index') }}">Anggota</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="profile-card">
                <div class="card-body">
                    <form action="{{ route('anggota.simpan') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Informasi Umum -->
                        <div class="form-group">
                            <label for="nama">Nama Lengkap *</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                value="{{ old('nama') }}" required>
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
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" required>
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

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" name="password" id="password" class="form-control" required>
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
                            <label for="password_confirmation">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" required>
                        </div>

                        <!-- Level User -->
                        <div class="form-group">
                            <label for="level">Level *</label>
                            <select name="level" id="level" class="form-control" required>
                                <option value="">-- Pilih Level --</option>
                                <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="siswa" {{ old('level') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="guru" {{ old('level') == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="staff" {{ old('level') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('level')
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

                        <!-- Informasi Khusus Sesuai Level -->
                        <div id="admin-fields" class="level-fields" style="display: none;">
                            <div class="form-group">
                                <label for="nip">NIP *</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}">
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
                        </div>

                        <div id="siswa-fields" class="level-fields" style="display: none;">
                            <div class="form-group">
                                <label for="nisn">NISN *</label>
                                <input type="number" name="nisn" id="nisn" class="form-control"
                                    value="{{ old('nisn') }}">
                                @error('nisn')
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
                                <label for="kelas">Kelas *</label>
                                <select name="kelas" id="kelas" class="form-control">
                                    <option value="">Pilih Kelas</option>
                                    <option value="VII A" {{ old('kelas') == 'VII A' ? 'selected' : '' }}>VII A</option>
                                    <option value="VII B" {{ old('kelas') == 'VII B' ? 'selected' : '' }}>VII B</option>
                                    <option value="VIII A" {{ old('kelas') == 'VIII A' ? 'selected' : '' }}>VIII A
                                    </option>
                                    <option value="VIII B" {{ old('kelas') == 'VIII B' ? 'selected' : '' }}>VIII B
                                    </option>
                                    <option value="IX A" {{ old('kelas') == 'IX A' ? 'selected' : '' }}>IX A</option>
                                    <option value="IX B" {{ old('kelas') == 'IX B' ? 'selected' : '' }}>IX B</option>
                                </select>
                                @error('kelas')
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
                        </div>

                        <div id="guru-fields" class="level-fields" style="display: none;">
                            <div class="form-group">
                                <label for="nip">NIP *</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}">
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
                                <label for="mata_pelajaran">Mata Pelajaran *</label>
                                <select name="mata_pelajaran" id="mata_pelajaran" class="form-control">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <option value="Matematika"
                                        {{ old('mata_pelajaran') == 'Matematika' ? 'selected' : '' }}>Matematika</option>
                                    <option value="Bahasa Indonesia"
                                        {{ old('mata_pelajaran') == 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa
                                        Indonesia</option>
                                    <option value="Bahasa Inggris"
                                        {{ old('mata_pelajaran') == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris
                                    </option>
                                    <option value="Bahasa Arab"
                                        {{ old('mata_pelajaran') == 'Bahasa Arab' ? 'selected' : '' }}>Bahasa Arab</option>
                                    <option value="Bahasa Sunda"
                                        {{ old('mata_pelajaran') == 'Bahasa Sunda' ? 'selected' : '' }}>Bahasa Sunda
                                    </option>
                                    <option value="IPA" {{ old('mata_pelajaran') == 'IPA' ? 'selected' : '' }}>IPA
                                    </option>
                                    <option value="IPS" {{ old('mata_pelajaran') == 'IPS' ? 'selected' : '' }}>IPS
                                    </option>
                                    <option value="Pendidikan Agama Islam"
                                        {{ old('mata_pelajaran') == 'Pendidikan Agama Islam' ? 'selected' : '' }}>
                                        Pendidikan Agama Islam</option>
                                    <option value="Fiqih" {{ old('mata_pelajaran') == 'Fiqih' ? 'selected' : '' }}>Fiqih
                                    </option>
                                    <option value="Akidah Akhlak"
                                        {{ old('mata_pelajaran') == 'Akidah Akhlak' ? 'selected' : '' }}>Akidah Akhlak
                                    </option>
                                </select>
                                @error('mata_pelajaran')
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
                        </div>

                        <div id="staff-fields" class="level-fields" style="display: none;">
                            <div class="form-group">
                                <label for="nip">NIP *</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip') }}">
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
                                <label for="bagian">Bagian *</label>
                                <select name="bagian" id="bagian" class="form-control">
                                    <option value="">Pilih Bagian</option>
                                    <option value="Kepala Tata Usaha"
                                        {{ old('bagian') == 'Kepala Tata Usaha' ? 'selected' : '' }}>Kepala Tata Usaha
                                    </option>
                                    <option value="Staff Keuangan"
                                        {{ old('bagian') == 'Staff Keuangan' ? 'selected' : '' }}>Staff Keuangan</option>
                                    <option value="Staff Laboratorium"
                                        {{ old('bagian') == 'Staff Laboratorium' ? 'selected' : '' }}>Staff Laboratorium
                                    </option>
                                    <option value="Staff Keamanan"
                                        {{ old('bagian') == 'Staff Keamanan' ? 'selected' : '' }}>Staff Keamanan</option>
                                    <option value="Staff Kebersihan"
                                        {{ old('bagian') == 'Staff Kebersihan' ? 'selected' : '' }}>Staff Kebersihan
                                    </option>
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
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir') }}" required>
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
                            <label for="alamat">Alamat *</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
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
                            <label for="no_telepon">Nomor Telepon *</label>
                            <input type="text" name="no_telepon" id="no_telepon" class="form-control"
                                value="{{ old('no_telepon') }}" required>
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

                        <!-- Bagian Upload Foto dengan Preview -->
                        <div class="form-group">
                            <label for="foto">Foto Profil</label>
                            <input type="file" name="foto" id="foto" class="form-control"
                                onchange="previewImage(event)">
                            <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 3MB.</small>

                            <!-- Preview Foto -->
                            <div class="mt-3" id="preview-container" style="display: none;">
                                <label for="preview" style="margin-top: 15px;">Preview Foto:</label>
                                <div class="preview-box"
                                    style="border: 2px solid #ddd; padding: 5px; display: inline-block; border-radius: 5px; margin-top: 5px;">
                                    <img id="preview" src="{{ asset('assets/img/boy.png') }}" alt="Preview Foto"
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

                        <!-- Tombol Submit -->
                        <div class="form-group text-end">
                            <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/anggota/anggota.js') }}"></script>
@endsection
