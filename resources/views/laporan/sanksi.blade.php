@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Dashboard Laporan Sanksi</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a class="active">Laporan Sanksi</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Sanksi -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalSanksi }}</h2>
                        <p>Total Sanksi</p>
                    </div>
                    <i class='bx bxs-error icon'></i>
                </div>
            </div>
            <!-- Card Belum Bayar -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $belumBayar }}</h2>
                        <p>Belum Bayar</p>
                    </div>
                    <i class='bx bxs-error-circle icon'></i>
                </div>
            </div>
            <!-- Card Sudah Bayar -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $sudahBayar }}</h2>
                        <p>Sudah Bayar</p>
                    </div>
                    <i class='bx bxs-check-circle icon'></i>
                </div>
            </div>
            <!-- Card Total Denda -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>Rp {{ number_format($totalDenda, 0, ',', '.') }}</h2>
                        <p>Total Denda</p>
                    </div>
                    <i class='bx bxs-coin icon'></i>
                </div>
            </div>
        </div>

        <!-- Statistik Berdasarkan Level -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Statistik Sanksi Berdasarkan Level Anggota</h3>
                </div>

                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                    @foreach ($statistikLevel as $level)
                        <div
                            style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                            <div style="text-align: center;">
                                @if ($level->level == 'siswa')
                                    <i class='bx bxs-user'
                                        style="font-size: 3rem; color: #007bff; margin-bottom: 1rem;"></i>
                                @elseif($level->level == 'guru')
                                    <i class='bx bxs-user-badge'
                                        style="font-size: 3rem; color: #28a745; margin-bottom: 1rem;"></i>
                                @else
                                    <i class='bx bxs-user-detail'
                                        style="font-size: 3rem; color: #ffc107; margin-bottom: 1rem;"></i>
                                @endif
                                <h4>{{ ucfirst($level->level) }}</h4>
                                <p style="color: var(--dark-grey); margin-bottom: 20px;">
                                    <strong>Total Sanksi:</strong> {{ $level->total_sanksi }}<br>
                                    <strong>Belum Bayar:</strong> {{ $level->belum_bayar }}<br>
                                    <strong>Sudah Bayar:</strong> {{ $level->sudah_bayar }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Laporan -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Menu Laporan Sanksi</h3>
                </div>

                <!-- Menu Grid -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                    <!-- Card Belum Bayar -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-error-circle'
                                style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                            <h4>Laporan Sanksi Belum Bayar</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat daftar sanksi yang belum dibayar
                                oleh peminjam</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.sanksi.belum_bayar') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card Sudah Bayar -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-check-circle'
                                style="font-size: 3rem; color: #28a745; margin-bottom: 1rem;"></i>
                            <h4>Laporan Sanksi Sudah Bayar</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat riwayat sanksi yang sudah dibayar
                            </p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.sanksi.sudah_bayar') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
