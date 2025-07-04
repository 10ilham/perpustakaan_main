@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h1 class="title">Siswa Profile</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <!-- Profil Siswa -->
                <div class="col-12 col-lg-4">
                    <div class="profile-card">
                        <div class="card-body text-center">
                            <div class="avatar">
                                @if ($siswa->foto)
                                    <img src="{{ asset('assets/img/siswa_foto/' . $siswa->foto) }}" alt="Foto Profil">
                                @else
                                    <img src="{{ asset('assets/img/boy.png') }}" alt="Default foto">
                                @endif
                            </div>
                            <h3 class="mt-3">{{ $siswa->user->nama }}</h3>
                            <p class="text-center">
                                <span class="badge badge-outline-success">Siswa</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Data Profil -->
                <div class="col-12 col-lg-8">
                    <div class="profile-card">
                        <div class="card-body">
                            <form class="profile-display">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" id="name" class="form-control"
                                        value="{{ $siswa->user->nama }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control"
                                        value="{{ $siswa->user->email }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nisn">NISN</label>
                                    <input type="text" id="nisn" class="form-control" value="{{ $siswa->nisn }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="kelas">Kelas</label>
                                    <input type="text" id="kelas" class="form-control" value="{{ $siswa->kelas }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" class="form-control"
                                        value="{{ $siswa->tanggal_lahir }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" class="form-control" rows="3" readonly>{{ $siswa->alamat }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="no_telepon">Nomor Telepon</label>
                                    <input type="text" id="no_telepon" class="form-control"
                                        value="{{ $siswa->no_telepon }}" readonly>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <a href="{{ route('siswa.profile.edit') }}" class="btn btn-success">
                                            <i class="bx bx-edit"></i>Edit Profil
                                        </a>
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
