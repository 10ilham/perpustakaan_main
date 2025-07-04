@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="title">Edit Data Anggota</h1>
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
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="profile-card">
                <div class="card-body">
                    <form action="{{ route('anggota.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Preview Foto Saat Ini -->
                        @php
                            $defaultImage = asset('assets/img/boy.png');
                            $photoPath = $defaultImage;

                            if ($user->level === 'admin' && isset($profileData->foto)) {
                                $photoPath = asset('assets/img/admin_foto/' . $profileData->foto);
                            } elseif ($user->level === 'siswa' && isset($profileData->foto)) {
                                $photoPath = asset('assets/img/siswa_foto/' . $profileData->foto);
                            } elseif ($user->level === 'guru' && isset($profileData->foto)) {
                                $photoPath = asset('assets/img/guru_foto/' . $profileData->foto);
                            } elseif ($user->level === 'staff' && isset($profileData->foto)) {
                                $photoPath = asset('assets/img/staff_foto/' . $profileData->foto);
                            }
                        @endphp

                        <div class="text-center mb-4">
                            <div class="avatar mb-3">
                                <img src="{{ $photoPath }}" alt="Foto Profil"
                                    style="max-width: 120px; border-radius: 50%;">
                            </div>
                            <h3>{{ $user->nama }}</h3>
                            <p class="text-muted">{{ ucfirst($user->level) }}</p>
                        </div>

                        <!-- Informasi Umum -->
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control"
                                value="{{ old('nama', $user->nama) }}" required>
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
                                value="{{ old('email', $user->email) }}" required>
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

                        <!-- Informasi Khusus Sesuai Level -->
                        @if ($user->level === 'admin')
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip', $profileData->nip ?? '') }}">
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
                        @elseif($user->level === 'siswa')
                            <div class="form-group">
                                <label for="nisn">NISN</label>
                                <input type="number" name="nisn" id="nisn" class="form-control"
                                    value="{{ old('nisn', $profileData->nisn ?? '') }}">
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
                                <label for="kelas">Kelas</label>
                                <select name="kelas" id="kelas" class="form-control">
                                    <option value="">Pilih Kelas</option>
                                    <option value="VII A"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'VII A' ? 'selected' : '' }}>VII A
                                    </option>
                                    <option value="VII B"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'VII B' ? 'selected' : '' }}>VII B
                                    </option>
                                    <option value="VIII A"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'VIII A' ? 'selected' : '' }}>VIII A
                                    </option>
                                    <option value="VIII B"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'VIII B' ? 'selected' : '' }}>VIII B
                                    </option>
                                    <option value="IX A"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'IX A' ? 'selected' : '' }}>IX A
                                    </option>
                                    <option value="IX B"
                                        {{ old('kelas', $profileData->kelas ?? '') == 'IX B' ? 'selected' : '' }}>IX B
                                    </option>
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
                        @elseif($user->level === 'guru')
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip', $profileData->nip ?? '') }}">
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
                                <label for="mata_pelajaran">Mata Pelajaran</label>
                                <select name="mata_pelajaran" id="mata_pelajaran" class="form-control">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <option value="Matematika"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Matematika' ? 'selected' : '' }}>
                                        Matematika</option>
                                    <option value="Bahasa Indonesia"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Bahasa Indonesia' ? 'selected' : '' }}>
                                        Bahasa Indonesia</option>
                                    <option value="Bahasa Inggris"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Bahasa Inggris' ? 'selected' : '' }}>
                                        Bahasa Inggris</option>
                                    <option value="Bahasa Arab"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Bahasa Arab' ? 'selected' : '' }}>
                                        Bahasa Arab</option>
                                    <option value="Bahasa Sunda"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Bahasa Sunda' ? 'selected' : '' }}>
                                        Bahasa Sunda</option>
                                    <option value="IPA"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'IPA' ? 'selected' : '' }}>
                                        IPA</option>
                                    <option value="IPS"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'IPS' ? 'selected' : '' }}>
                                        IPS</option>
                                    <option value="Pendidikan Agama Islam"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Pendidikan Agama Islam' ? 'selected' : '' }}>
                                        Pendidikan Agama Islam</option>
                                    <option value="Fiqih"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Fiqih' ? 'selected' : '' }}>
                                        Fiqih</option>
                                    <option value="Akidah Akhlak"
                                        {{ old('mata_pelajaran', $profileData->mata_pelajaran ?? '') == 'Akidah Akhlak' ? 'selected' : '' }}>
                                        Akidah Akhlak</option>
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
                        @elseif($user->level === 'staff')
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="number" name="nip" id="nip" class="form-control"
                                    value="{{ old('nip', $profileData->nip ?? '') }}">
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
                                <select name="bagian" id="bagian" class="form-control">
                                    <option value="">Pilih Bagian</option>
                                    <option value="Kepala Tata Usaha"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Kepala Tata Usaha' ? 'selected' : '' }}>
                                        Kepala Tata Usaha</option>
                                    <option value="Tata Usaha"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Tata Usaha' ? 'selected' : '' }}>
                                        Tata Usaha</option>
                                    <option value="Staff Keuangan"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Staff Keuangan' ? 'selected' : '' }}>
                                        Staff Keuangan</option>
                                    <option value="Staff Laboratorium"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Staff Laboratorium' ? 'selected' : '' }}>
                                        Staff Laboratorium</option>
                                    <option value="Staff Keamanan"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Staff Keamanan' ? 'selected' : '' }}>
                                        Staff Keamanan</option>
                                    <option value="Staff Kebersihan"
                                        {{ old('bagian', $profileData->bagian ?? '') == 'Staff Kebersihan' ? 'selected' : '' }}>
                                        Staff Kebersihan</option>
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
                        @endif

                        <!-- Informasi Kontak -->
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir', $profileData->tanggal_lahir ?? '') }}">
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
                            <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ old('alamat', $profileData->alamat ?? '') }}</textarea>
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
                                value="{{ old('no_telepon', $profileData->no_telepon ?? '') }}">
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

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Password baru">
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
                                onchange="previewImage(event)" value="{{ old('foto', $profileData->foto ?? '') }}">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto profil.</small>

                            <!-- Preview Foto -->
                            <div class="mt-3" id="preview-container" style="display: none;">
                                <label for="preview" style="margin-top: 15px;">Preview Foto:</label>
                                <div class="preview-box"
                                    style="border: 2px solid #ddd; padding: 5px; display: inline-block; border-radius: 5px; margin-top: 5px;">
                                    <img id="preview" src="{{ $photoPath }}" alt="Preview Foto"
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
                            @if (isset($ref) && $ref == 'detail')
                                <a href="{{ route('anggota.detail', $user->id) }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back"></i> Batal
                                </a>
                                <!-- Menyimpan parameter ref untuk redirect setelah update -->
                                <input type="hidden" name="ref" value="{{ $ref }}">
                            @else
                                <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back"></i> Batal
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
