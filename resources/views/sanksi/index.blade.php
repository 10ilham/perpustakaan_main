@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Sanksi & Denda</div>
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
            <li><a class="active">Sanksi</a></li>
        </ul>

        <br>
        <!-- Area Filter untuk Sanksi-->

        <div class="filter">
            <div class="card">
                <div class="head">
                    <h3>Filter Sanksi</h3>
                </div>
                <div class="form-group" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                    {{-- Filter Anggota - Hanya untuk Admin --}}
                    @if (auth()->user()->level == 'admin')
                        <select id="filterAnggota" class="form-control" style="max-width: 180px; height: 40px;">
                            <option value="">Semua Anggota</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="staff">Staff</option>
                        </select>
                    @endif

                    <select id="filterSanksi" class="form-control" style="max-width: 180px; height: 40px;">
                        <option value="">Semua Sanksi</option>
                        <option value="keterlambatan">Keterlambatan</option>
                        <option value="rusak_hilang">Rusak/Hilang</option>
                    </select>

                    <select id="filterStatus" class="form-control" style="max-width: 180px; height: 40px;">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sudah_bayar">Sudah Bayar</option>
                    </select>

                    <button type="submit" class="btn-download btn-filter" style="padding: 5px 15px;"
                        onclick="applyFilters()">
                        <i class='bx bx-filter'></i> Filter
                    </button>

                    <button id="resetBtn" class="btn btn-secondary" style="padding: 5px 15px; display: none;"
                        onclick="resetFilters()">
                        <i class='bx bx-reset'></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Sanksi</h3>
                </div>
                <div class="table-responsive">
                    <table id="sanksiTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                @if (auth()->user()->level == 'admin')
                                    <th>Level</th>
                                @endif
                                <th>Buku</th>
                                <th>Tanggal Sanksi</th>
                                <th>Jenis Sanksi</th>
                                <th>Hari Terlambat</th>
                                <th>Denda Keterlambatan</th>
                                <th>Denda Kerusakan</th>
                                <th>Total Denda</th>
                                <th>Status Bayar</th>
                                @if (auth()->user()->level == 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sanksi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->peminjaman->user->nama ?? $item->peminjaman->user->name }}</strong><br>
                                    </td>
                                    @if (auth()->user()->level == 'admin')
                                        <td>
                                            <strong>{{ ucfirst($item->peminjaman->user->level) }}</strong>
                                        </td>
                                    @endif
                                    <td>
                                        <strong>{{ $item->peminjaman->buku->judul }}</strong><br>
                                        {{-- <small class="text-muted">{{ $item->peminjaman->buku->kode_buku }}</small> --}}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $jenisSanksi = explode(',', $item->jenis_sanksi);
                                        @endphp
                                        @foreach ($jenisSanksi as $jenis)
                                            <span
                                                class="badge
                                                @if ($jenis == 'keterlambatan') badge-warning
                                                @elseif($jenis == 'rusak_hilang') badge-danger
                                                @else badge-dark @endif
                                            ">
                                                @if ($jenis == 'keterlambatan')
                                                    Keterlambatan
                                                @elseif($jenis == 'rusak_hilang')
                                                    Rusak/Hilang
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $jenis)) }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($item->hari_terlambat > 0)
                                            <span>{{ $item->hari_terlambat }} hari</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->denda_keterlambatan > 0)
                                            <span class="text-warning">Rp
                                                {{ number_format($item->denda_keterlambatan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->denda_kerusakan > 0)
                                            <span class="text-danger">Rp
                                                {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-primary">Rp
                                            {{ number_format($item->total_denda, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if ($item->status_bayar == 'sudah_bayar')
                                            <span class="badge badge-success">Sudah Bayar</span>
                                        @else
                                            <span class="badge badge-danger">Belum Bayar</span>
                                        @endif
                                    </td>

                                    @if (auth()->user()->level == 'admin')
                                        <td>
                                            @if ($item->status_bayar == 'belum_bayar')
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="showPaymentModal('{{ $item->id }}')">
                                                    <i class="bx bx-check"></i> Konfirmasi Bayar
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($sanksi->count() > 0)
                    <div class="mt-3">
                        <div class="row" id="dendaCards">
                            <div class="col-md-4" id="cardBelumBayar">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Belum Dibayar</h6>
                                        <h4 class="text-danger" id="totalBelumBayar">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'belum_bayar')->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="cardSudahBayar">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Denda yang Sudah Dibayar</h6>
                                        <h4 class="text-success" id="totalSudahBayar">
                                            Rp
                                            {{ number_format($sanksi->where('status_bayar', 'sudah_bayar')->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="cardTotalDenda">
                                <div class="card">
                                    <div class="card-body">
                                        <h6>Total Keseluruhan Denda</h6>
                                        <h4 class="text-primary" id="totalKeseluruhan">
                                            Rp {{ number_format($sanksi->sum('total_denda'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Konfirmasi Pembayaran -->
        <div class="modal fade bootstrap-modal" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Konfirmasi Pembayaran Denda</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Pastikan pembayaran denda telah diterima sebelum mengkonfirmasi.</p>
                        <p>Apakah Anda yakin ingin mengkonfirmasi pembayaran denda ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="paymentForm" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        // Set global variables untuk sanksi.js
        window.userLevel = '{{ auth()->user()->level }}';
        window.sanksiPaymentRoute = '{{ route('sanksi.bayar', ':id') }}';
    </script>
    <script src="{{ asset('assets/js/sanksi/sanksi.js') }}"></script>
@endsection
