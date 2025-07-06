@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Kategori</div>
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
            <li><a class="active">Kategori</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Kategori Buku</h3>
                    @if (auth()->user()->level == 'admin')
                        <a href="{{ route('kategori.tambah') }}" class="btn btn-success">
                            <i class="bx bx-plus-circle"></i>
                            <span>Tambah Kategori</span>
                        </a>
                    @endif
                </div>

                <div class="table-responsive p-3">
                    <table id="dataTableKategori" class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Buku</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nama_kategori }}</td>
                                    <td>{{ $item->deskripsi ?: 'Tidak ada deskripsi' }}</td>
                                    <td>{{ $item->buku->count() }} buku</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('kategori.detail', $item->id) }}" class="btn btn-sm btn-info"
                                                title="Detail">
                                                <i class="bx bx-info-circle"></i>
                                            </a>

                                            @if (auth()->user()->level == 'admin')
                                                <a href="{{ route('kategori.edit', $item->id) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bx bxs-edit"></i>
                                                </a>

                                                <form action="{{ route('kategori.hapus', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-action="{{ route('kategori.hapus', $item->id) }}"
                                                        title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                    Apakah Anda yakin ingin menghapus item ini?
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

@section('scripts')
    <script>
        // Set level user untuk JavaScript
        window.userLevel = '{{ auth()->user()->level }}';
    </script>
    <script src="{{ asset('assets/js/kategori/kategori.js') }}"></script>
@endsection
