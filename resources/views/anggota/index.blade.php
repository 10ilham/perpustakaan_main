@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Anggota</div>
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'siswa')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'guru')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'staff')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @endif
            <li class="divider">/</li>
            <li><a href="#" class="active">Anggota</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Anggota -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $users->count() }}</h2>
                        <p>Total Anggota</p>
                    </div>
                    <i class='bx bxs-user icon'></i>
                </div>
            </div>

            <!-- Card Filter -->
            <div class="card">
                <div class="head">
                    <h3>Filter Anggota</h3>
                </div>
                <form action="{{ route('anggota.index') }}" method="GET" class="form-group" style="margin-top: 10px;">
                    <select name="level" id="level" onchange="this.form.submit()">
                        @foreach ($levels as $key => $value)
                            <?php
                            $selected = '';
                            $icon = '';
                            if (request('level') == $key) {
                                $selected = 'selected';
                            }

                            // Set icon berdasarkan level
                            if ($key == 'admin') {
                                $icon = '<span class="select-icon">👨‍💼</span>';
                            } elseif ($key == 'siswa') {
                                $icon = '<span class="select-icon">👨‍🎓</span>';
                            } elseif ($key == 'guru') {
                                $icon = '<span class="select-icon">👨‍🏫</span>';
                            } elseif ($key == 'staff') {
                                $icon = '<span class="select-icon">👨‍💼</span>';
                            } elseif ($key == 'all') {
                                $icon = '<span class="select-icon">👥</span>';
                            }
                            ?>
                            <option value="{{ $key }}" {{ $selected }}>{!! $icon !!}<span
                                    class="option-label">{{ $value }}</span></option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Anggota</h3>
                    <a href="{{ route('anggota.tambah') }}" class="btn btn-success d-flex align-items-center">
                        <i class="bx bx-plus-circle"></i>
                        <span>Tambah Anggota</span>
                    </a>
                </div>

                <div class="table-responsive p-3">
                    <table id="dataTable" class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>NIP/NISN</th>
                                <th>Tombol Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $defaultImage = asset('assets/img/boy.png');
                                            $photoPath = $defaultImage;

                                            if ($user->level === 'admin') {
                                                $profile = App\Models\AdminModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/admin_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'siswa') {
                                                $profile = App\Models\SiswaModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/siswa_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'guru') {
                                                $profile = App\Models\GuruModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/guru_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'staff') {
                                                $profile = App\Models\StaffModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/staff_foto/' . $profile->foto);
                                                }
                                            }
                                        @endphp
                                        <img src="{{ $photoPath }}" alt="Foto Profil">
                                    </td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->level === 'admin')
                                            <span class="badge badge-outline-primary">Admin</span>
                                        @elseif($user->level === 'siswa')
                                            <span class="badge badge-outline-success">Siswa</span>
                                        @elseif($user->level === 'guru')
                                            <span class="badge badge-outline-warning">Guru</span>
                                        @elseif($user->level === 'staff')
                                            <span class="badge badge-outline-secondary">Staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $idAnggota = '-';

                                            if ($user->level === 'admin') {
                                                $profile = App\Models\AdminModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            } elseif ($user->level === 'siswa') {
                                                $profile = App\Models\SiswaModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nisn ?? '-';
                                                }
                                            } elseif ($user->level === 'guru') {
                                                $profile = App\Models\GuruModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            } elseif ($user->level === 'staff') {
                                                $profile = App\Models\StaffModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            }
                                        @endphp
                                        {{ $idAnggota }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <!-- Tombol Detail -->
                                            <a href="{{ route('anggota.detail', $user->id) }}" class="btn btn-sm btn-info"
                                                title="Detail" data-toggle="tooltip">
                                                <i class="bx bx-info-circle"></i>
                                            </a>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('anggota.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                                title="Edit" data-toggle="tooltip">
                                                <i class='bx bxs-edit'></i>
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('anggota.hapus', $user->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-action="{{ route('anggota.hapus', $user->id) }}" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade bootstrap-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus anggota ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
