@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h1 class="title">Staff Profile</h1>
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
                <!-- Profil Staff -->
                <div class="col-12 col-lg-4">
                    <div class="profile-card">
                        <div class="card-body text-center">
                            <div class="avatar">
                                @if ($staff->foto)
                                    <img src="{{ asset('assets/img/staff_foto/' . $staff->foto) }}" alt="Foto Profil">
                                @else
                                    <img src="{{ asset('assets/img/boy.png') }}" alt="Default foto">
                                @endif
                            </div>
                            <h3 class="mt-3">{{ $staff->user->nama }}</h3>
                            <p class="text-center">
                                <span class="badge badge-outline-secondary">Staff</span>
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
                                        value="{{ $staff->user->nama }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control"
                                        value="{{ $staff->user->email }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nip">NIP</label>
                                    <input type="text" id="nip" class="form-control" value="{{ $staff->nip }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="bagian">Bagian</label>
                                    <input type="text" id="bagian" class="form-control" value="{{ $staff->bagian }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="text" id="tanggal_lahir" class="form-control"
                                        value="{{ $staff->tanggal_lahir }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" class="form-control" rows="3" readonly>{{ $staff->alamat }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="no_telepon">Nomor Telepon</label>
                                    <input type="text" id="no_telepon" class="form-control"
                                        value="{{ $staff->no_telepon }}" readonly>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <a href="{{ route('staff.profile.edit') }}" class="btn btn-success">
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
