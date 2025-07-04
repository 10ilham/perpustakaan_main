@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h1 class="title">Detail Anggota</h1>
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
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Profil Anggota -->
                <div class="col-12 col-lg-4">
                    <div class="profile-card">
                        <div class="card-body text-center">
                            <div class="avatar">
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
                                <img src="{{ $photoPath }}" alt="Foto Profil">
                            </div>
                            <h3 class="mt-3">{{ $user->nama }}</h3>
                            <p class="text-muted">
                                @if ($user->level === 'admin')
                                    <span class="badge badge-outline-primary">Admin</span>
                                @elseif($user->level === 'siswa')
                                    <span class="badge badge-outline-success">Siswa</span>
                                @elseif($user->level === 'guru')
                                    <span class="badge badge-outline-warning">Guru</span>
                                @elseif($user->level === 'staff')
                                    <span class="badge badge-outline-secondary">Staff</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detail Informasi -->
                <div class="col-12 col-lg-8">
                    <div class="profile-card">
                        <div class="card-body">
                            <form class="profile-display">
                                <!-- Informasi Umum -->
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" id="nama" class="form-control" value="{{ $user->nama }}"
                                        readonly>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" value="{{ $user->email }}"
                                        readonly>
                                </div>

                                <!-- Informasi Khusus Sesuai Level -->
                                @if ($user->level === 'admin')
                                    <div class="form-group">
                                        <label for="nip">NIP</label>
                                        <input type="text" id="nip" class="form-control"
                                            value="{{ $profileData->nip ?? '-' }}" readonly>
                                    </div>
                                @elseif($user->level === 'siswa')
                                    <div class="form-group">
                                        <label for="nisn">NISN</label>
                                        <input type="text" id="nisn" class="form-control"
                                            value="{{ $profileData->nisn ?? '-' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="kelas">Kelas</label>
                                        <input type="text" id="kelas" class="form-control"
                                            value="{{ $profileData->kelas ?? '-' }}" readonly>
                                    </div>
                                @elseif($user->level === 'guru')
                                    <div class="form-group">
                                        <label for="nip">NIP</label>
                                        <input type="text" id="nip" class="form-control"
                                            value="{{ $profileData->nip ?? '-' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="mapel">Mata Pelajaran</label>
                                        <input type="text" id="mapel" class="form-control"
                                            value="{{ $profileData->mapel ?? '-' }}" readonly>
                                    </div>
                                @elseif($user->level === 'staff')
                                    <div class="form-group">
                                        <label for="nip">NIP</label>
                                        <input type="text" id="nip" class="form-control"
                                            value="{{ $profileData->nip ?? '-' }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="bagian">Bagian</label>
                                        <input type="text" id="bagian" class="form-control"
                                            value="{{ $profileData->bagian ?? '-' }}" readonly>
                                    </div>
                                @endif

                                <!-- Informasi Kontak -->
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" class="form-control"
                                        value="{{ $profileData->tanggal_lahir ?? '-' }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" class="form-control" rows="3" readonly>{{ $profileData->alamat ?? '-' }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="no_telepon">Nomor Telepon</label>
                                    <input type="text" id="no_telepon" class="form-control"
                                        value="{{ $profileData->no_telepon ?? '-' }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="created_at">Terdaftar Pada</label>
                                    <input type="text" id="created_at" class="form-control"
                                        value="{{ $user->created_at->format('d F Y H:i') }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" id="updated_at" class="form-control"
                                        value="{{ $user->updated_at->format('d F Y H:i') }}" readonly>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="form-group">
                                    <div>
                                        <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                        <a href="{{ route('anggota.edit', ['id' => $user->id, 'ref' => 'detail']) }}"
                                            class="btn btn-success">
                                            <i class="bx bx-edit"></i> Edit
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

    <!-- Riwayat Peminjaman kecualikan admin dengan tanda (!) -->
    @if ($user->level !== 'admin')
        <div class="row mt-4" style="width: 100%; padding: 0 20px 20px 20px; margin-top: 0 !important;">
            <div class="col-12">
                <div class="profile-card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Daftar Riwayat Peminjaman</h4>
                        <div class="table-responsive">
                            <table id="dataTable" class="table align-items-center table-flush table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Peminjaman</th>
                                        <th>Judul Buku</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Tgl Batas Kembali</th>
                                        <th>Tgl Pengembalian</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($peminjaman as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->no_peminjaman }}</td>
                                            <td>{{ $item->buku->judul }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @if ($item->tanggal_pengembalian)
                                                    {{ \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->status == 'Diproses')
                                                    <span class="badge" style="color: #0077ff;">{{ $item->status }}
                                                    </span>
                                                @elseif ($item->status == 'Dipinjam')
                                                    <span class="badge" style="color: #ffc107;">{{ $item->status }}
                                                    </span>
                                                @elseif ($item->status == 'Dikembalikan')
                                                    @if ($item->is_terlambat)
                                                        <span class="badge" style="color: #28a745;">{{ $item->status }}
                                                        </span>
                                                        <span class="badge" style="color: #dc3545;">Terlambat
                                                            ({{ $item->jumlah_hari_terlambat }} hari)
                                                        </span>
                                                    @else
                                                        <span class="badge" style="color: #28a745;">{{ $item->status }}
                                                        </span>
                                                    @endif
                                                @elseif ($item->status == 'Terlambat')
                                                    <span class="badge" style="color: #dc3545;">{{ $item->status }}
                                                        ({{ $item->is_late ? $item->late_days : '?' }} hari)
                                                    </span>
                                                @elseif ($item->status == 'Dibatalkan')
                                                    <span class="badge" style="color: #dc3545;">{{ $item->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('peminjaman.detail', ['id' => $item->id, 'ref' => 'anggota', 'anggota_id' => $user->id]) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="bx bx-info-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
