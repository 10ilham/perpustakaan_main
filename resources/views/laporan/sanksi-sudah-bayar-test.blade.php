@extends('layouts.app')

@section('content')
    <main>
        <h1 class="title">Laporan Sanksi Sudah Bayar - TEST</h1>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Test Page - Sanksi Sudah Bayar</h3>
                </div>

                <p>Total sanksi: {{ $totalSanksi ?? 0 }}</p>
                <p>Total denda: Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}</p>

                @if (isset($sanksi) && $sanksi->count() > 0)
                    <p>Ada {{ $sanksi->count() }} data sanksi sudah bayar</p>
                @else
                    <p>Tidak ada data sanksi sudah bayar</p>
                @endif
            </div>
        </div>
    </main>
@endsection
