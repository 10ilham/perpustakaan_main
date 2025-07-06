@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Dashboard Laporan</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="divider">/</li>
            <li><a class="active">Laporan</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Peminjaman -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalPeminjaman }}</h2>
                        <p>Total Peminjaman</p>
                    </div>
                    <i class='bx bxs-book-bookmark icon'></i>
                </div>
            </div>
            <!-- Card Belum Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $belumKembali }}</h2>
                        <p>Belum Dikembalikan</p>
                    </div>
                    <i class='bx bxs-book-open icon'></i>
                </div>
            </div>
            <!-- Card Sudah Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $sudahKembali }}</h2>
                        <p>Sudah Dikembalikan</p>
                    </div>
                    <i class='bx bxs-book-alt icon'></i>
                </div>
            </div>
            <!-- Card Terlambat -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $terlambat }}</h2>
                        <p>Terlambat</p>
                    </div>
                    <i class='bx bxs-error-circle icon'></i>
                </div>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Menu Laporan</h3>
                </div>

                <!-- Menu Grid -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                    <!-- Card Belum Dikembalikan -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-book-open' style="font-size: 3rem; color: #ff6b6b; margin-bottom: 1rem;"></i>
                            <h4>Laporan Belum Dikembalikan</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat daftar buku yang belum
                                dikembalikan oleh peminjam</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.belum_kembali') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card Sudah Dikembalikan -->
                    <div
                        style="background: var(--grey); padding: 24px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                        <div style="text-align: center;">
                            <i class='bx bxs-book-alt' style="font-size: 3rem; color: #4ecdc4; margin-bottom: 1rem;"></i>
                            <h4>Laporan Sudah Dikembalikan</h4>
                            <p style="color: var(--dark-grey); margin-bottom: 20px;">Lihat riwayat buku yang sudah
                                dikembalikan</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="{{ route('laporan.sudah_kembali') }}" class="btn-download">
                                    <i class='bx bx-show'></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Bulanan -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Statistik Peminjaman</h3>
                    <div class="period-filter">
                        <select id="period-filter" class="filter-select">
                            <option value="day" selected>1 Hari</option>
                            <option value="week">1 Minggu</option>
                            <option value="month">1 Bulan</option>
                            <option value="6months">6 Bulan</option>
                            <option value="year">1 Tahun</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart Statistik Level -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Distribusi Peminjaman Per Level</h3>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="levelChart"></canvas>
                        <div id="levelChartRange" class="chart-range-info">
                            <!-- Period range will be updated by JavaScript -->
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 400px;">
                        <canvas id="statusChart"></canvas>
                        <div id="statusChartRange" class="chart-range-info">
                            <!-- Period range will be updated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('scripts')
    <script src="{{ asset('assets/js/laporan/laporan.js') }}"></script>
@endsection
