@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection

@section('content')
    <!-- MAIN -->
    <main>
        <h1 class="title">Dashboard</h1>
        <ul class="breadcrumbs">
            <li><a>Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
        <div class="info-data">
            <!-- Card Total Buku -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $totalBuku ?? '0' }}</h2>
                        <p>Total Buku</p>
                    </div>
                    <i class='bx bx-book icon'></i>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <a href="{{ route('buku.index') }}" class="label" style="color: #6f737b;">Lihat Detail</a>
                </div>
            </div>

            <!-- Card Sedang Dipinjam -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $dipinjam ?? '0' }}</h2>
                        <p>Sedang Dipinjam</p>
                    </div>
                    <i class='bx bxs-book-open icon'></i>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <a href="{{ route('peminjaman.index') }}" class="label" style="color: #6f737b;">Lihat Detail</a>
                </div>
            </div>

            <!-- Card Terlambat -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $terlambat ?? '0' }}</h2>
                        <p>Terlambat</p>
                    </div>
                    <i class='bx bx-time icon'></i>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <a href="{{ route('peminjaman.index') }}" class="label" style="color: #6f737b;">Lihat Detail</a>
                </div>
            </div>

            <!-- Card Dikembalikan -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $dikembalikan ?? '0' }}</h2>
                        <p>Dikembalikan</p>
                    </div>
                    <i class='bx bx-check-circle icon'></i>
                </div>
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <a href="{{ route('peminjaman.index') }}" class="label" style="color: #6f737b;">Lihat Detail</a>
                </div>
            </div>
        </div>

        <!-- Leaderboard Buku Populer -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Leaderboard Buku Terpopuler</h3>
                    <div class="menu">
                        <i class='bx bx-dots-horizontal-rounded icon'></i>
                        <ul class="menu-link">
                            <li><a href="{{ route('buku.index') }}">Lihat Semua Buku</a></li>
                        </ul>
                    </div>
                </div>
                <div class="leaderboard">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sampul</th>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Total Peminjaman</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($bukuPopuler) && count($bukuPopuler) > 0)
                                @foreach ($bukuPopuler as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($item->buku->foto)
                                                <img src="{{ asset('assets/img/buku/' . $item->buku->foto) }}"
                                                    alt="{{ $item->buku->judul }}"
                                                    style="width: 40px; height: 60px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('assets/img/default_buku.png') }}" alt="Default"
                                                    style="width: 40px; height: 60px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{ $item->buku->judul }}</td>
                                        <td>{{ $item->buku->pengarang }}</td>
                                        <td>{{ $item->total_peminjaman }} kali</td>
                                        <td>
                                            <a href="{{ route('buku.detail', ['id' => $item->buku->id, 'dashboard' => 'anggota']) }}" {{-- 'anggota' penamaan url ini bebas karena hanya sebagai referensi --}}
                                                class="btn btn-sm btn-info">
                                                <i class="bx bx-info-circle"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data peminjaman buku</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Peminjaman -->
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Laporan Peminjaman</h3>
                    <div class="filter-period">
                        <select id="period-filter" class="form-control">
                            <option value="day" selected>1 Hari</option>
                            <option value="week">1 Minggu</option>
                            <option value="month">1 Bulan</option>
                        </select>
                    </div>
                </div>
                <div class="chart">
                    <div id="chart"></div>
                </div>
            </div>
        </div>

        <!-- Other content sections can be removed/commented out -->
    </main>
    <!-- END MAIN -->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadChart('day'); // Default load 1 hari

            document.getElementById('period-filter').addEventListener('change', function() {
                const period = this.value;
                loadChart(period);
            });
        });

        function loadChart(period) {
            // Mendapatkan data berdasarkan periode
            fetch(`/anggota/chart-data?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    renderChart(data);
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                });
        }

        function renderChart(data) {
            // Destroy chart jika sudah ada
            if (window.chart) {
                window.chart.destroy();
            }

            const options = {
                series: [{
                    name: 'Peminjaman',
                    data: data.peminjaman || []
                }],
                chart: {
                    height: 350,
                    type: 'area'
                },
                colors: ['#1775f1'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                title: {
                    text: 'Grafik Riwayat Peminjaman Anda',
                    align: 'left'
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                xaxis: {
                    categories: data.labels || [],
                    title: {
                        text: 'Tanggal'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Peminjaman'
                    },
                    min: 0
                }
            };

            window.chart = new ApexCharts(document.querySelector("#chart"), options);
            window.chart.render();
        }
    </script>
@endsection
