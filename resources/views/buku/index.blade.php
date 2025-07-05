@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Buku Perpustakaan</div>
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
            <li><a class="active">Buku</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Buku -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalBuku }}</h2>
                        <p>Total Buku</p>
                    </div>
                    <i class='bx bxs-book icon'></i>
                </div>
            </div>
            <!-- Card Buku Tersedia -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $tersedia }}</h2>
                        <p>Tersedia</p>
                    </div>
                    <i class='bx bxs-book-open icon'></i>
                </div>
            </div>
            <!-- Card Buku Habis -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $habis }}</h2>
                        <p>Habis</p>
                    </div>
                    <i class='bx bxs-user-check icon'></i>
                </div>
            </div>
        </div>

        <div class="filter">
            <!-- Card Filter Buku -->
            <div class="card">
                <div class="head">
                    <h3>Filter Buku</h3>
                </div>
                <form action="{{ route('buku.index') }}" method="GET" class="form-group">
                    <select name="kategori" id="kategori" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Habis" {{ request('status') == 'Habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Buku</h3>
                    @if (auth()->user()->level == 'admin')
                        <a href="{{ route('buku.tambah') }}" class="btn btn-success d-flex align-items-center">
                            <i class="bx bx-plus-circle"></i>
                            <span>Tambah Buku</span>
                        </a>
                    @endif
                </div>

                <!-- Area pencarian dan tombol export dalam satu baris -->
                <div class="search-export-row mb-3">
                    <!-- Form pencarian dengan menyertakan parameter filter yang aktif -->
                    <form class="navbar-search me-3" action="{{ route('buku.index') }}" method="GET">
                        <div class="input-group">
                            @if (request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <input type="search" name="search" class="form-control bg-light border-1 small"
                                placeholder="Cari Judul Buku" aria-label="Search" aria-describedby="basic-addon2"
                                value="{{ request('search') }}" style="border-color: #244fbc;">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tombol untuk ekspor data -->
                    @if (auth()->user()->level == 'admin')
                        <div class="export-container">
                            <div class="export-buttons-container">
                                <button id="exportExcel" class="export-btn btn-outline-success">
                                    <i class="bx bx-file-blank"></i><span>Excel</span>
                                </button>
                                <button id="exportWord" class="export-btn btn-outline-info">
                                    <i class="bx bxs-file-doc"></i><span>Word</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row d-flex flex-wrap justify-content-center">
                    @forelse ($buku as $item)
                        <div class="col-auto my-2" style="width: 18rem;">
                            <div class="card card-buku mx-2 my-2 text-center align-items-center justify-content-center"
                                style="min-height: 28rem;">
                                @if ($item->foto)
                                    <img class="card-img-top" style="max-height: 180px;"
                                        src="{{ asset('assets/img/buku/' . $item->foto) }}" alt="{{ $item->judul }}">
                                @else
                                    <img class="card-img-top" style="height: 200px;"
                                        src="{{ asset('assets/img/default_buku.png') }}" alt="Default Book Cover">
                                @endif
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <div class="detail-buku">
                                        <h5 class="card-title">
                                            <a>
                                                {{ $item->judul }}
                                            </a>
                                        </h5>
                                        <p class="card-text m-0">Kode Buku: {{ $item->kode_buku }}</p>
                                        <p class="card-text m-0">Pengarang: {{ $item->pengarang }}</p>
                                        <p class="card-text m-0">Kategori:
                                            {{ $item->kategori->pluck('nama_kategori')->implode(', ') }}</p>
                                        <p class="card-text m-0">Status:
                                            @if ($item->status === 'Tersedia')
                                                <span class="badge badge-outline-success">{{ $item->status }}</span>
                                            @elseif ($item->status === 'Habis')
                                                <span class="badge badge-outline-danger">{{ $item->status }}</span>
                                            @else
                                                <span class="badge badge-outline-warning">{{ $item->status }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="button-area mt-3"
                                        style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('buku.detail', $item->id) }}?page={{ $buku->currentPage() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('status') ? '&status=' . request('status') : '' }}"
                                            class="btn btn-sm btn-info px-2"
                                            style="text-decoration: none; color: white; height: 31px; display: flex; align-items: center;">Detail</a>

                                        @if (auth()->user()->level == 'admin')
                                            <a href="{{ route('buku.edit', $item->id) }}?page={{ $buku->currentPage() }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('kategori') ? '&kategori=' . request('kategori') : '' }}{{ request('status') ? '&status=' . request('status') : '' }}"
                                                class="btn btn-sm btn-warning px-2"
                                                style="text-decoration: none; color: white; height: 31px; display: flex; align-items: center;">Edit</a>
                                            <button class="btn btn-sm btn-danger px-3 delete-btn" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-action="{{ route('buku.hapus', $item->id) }}"
                                                style="height: 31px; display: flex; align-items: center;">Hapus</button>
                                        @endif

                                        @if (auth()->user()->level == 'siswa' || auth()->user()->level == 'guru' || auth()->user()->level == 'staff')
                                            @if ($item->stok_buku > 0)
                                                <a href="{{ route('peminjaman.form', $item->id) }}"
                                                    class="btn btn-sm btn-success px-2"
                                                    style="text-decoration: none; color: white; height: 31px; display: flex; align-items: center;">Pinjam</a>
                                            @else
                                                <button class="btn btn-sm btn-warning px-2" disabled
                                                    style="text-decoration: none; height: 31px; display: flex; align-items: center;">Stok
                                                    Habis</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <h1 class="text-primary mt-3">Tidak ada buku</h1>
                    @endforelse
                </div>

                <div class="d-flex justify-content-between align-items-center mx-2 my-2" style="margin-top: 10px;">
                    @if ($buku instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <p class="text-primary my-2 mb-0">Menampilkan data ke-{{ $buku->firstItem() }} hingga
                            {{ $buku->lastItem() }}
                            dari {{ $buku->total() }} buku</p>
                        <div class="pagination-wrapper">
                            {{ $buku->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
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
    <script src="{{ asset('assets/js/buku/buku.js') }}"></script>
@endsection
