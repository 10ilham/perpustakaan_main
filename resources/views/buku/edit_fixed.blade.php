@extends('layouts.app')

@section('content')
    <div class="profile-page">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h1 class="page-title">Edit Buku</h1>
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
                            <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
                            <li class="divider">/</li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="profile-card">
                <div class="card-body">
                    <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Parameter referensi yang akan digunakan di controller --}}
                        @if (isset($ref) && $ref == 'kategori' && isset($kategori_id))
                            <input type="hidden" name="ref" value="{{ $ref }}">
                            <input type="hidden" name="kategori_id" value="{{ $kategori_id }}">
                        @endif

                        {{-- Parameter untuk pagination dan filter, ketika tombol simpan perubahan ditekan maka akan kembali ke halaman sebelumnya ketika sebelum diedit --}}
                        <input type="hidden" name="page" value="{{ request('page') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Informasi Dasar Buku -->
                                <div class="form-group">
                                    <label for="kode_buku">Kode Buku</label>
                                    <input type="text" name="kode_buku" id="kode_buku" class="form-control"
                                        value="{{ old('kode_buku', $buku->kode_buku) }}" required>
                                    @error('kode_buku')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="judul">Judul Buku</label>
                                    <input type="text" name="judul" id="judul" class="form-control"
                                        value="{{ old('judul', $buku->judul) }}" required>
                                    @error('judul')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kategori_id">Kategori</label>
                                    <select name="kategori_id[]" id="kategori_id" class="form-control" multiple>
                                        @foreach ($kategori as $kat)
                                            <option value="{{ $kat->id }}"
                                                {{ in_array($kat->id, old('kategori_id', $buku->kategori->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $kat->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="pengarang">Pengarang</label>
                                    <input type="text" name="pengarang" id="pengarang" class="form-control"
                                        value="{{ old('pengarang', $buku->pengarang) }}" required>
                                    @error('pengarang')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="penerbit">Penerbit</label>
                                    <input type="text" name="penerbit" id="penerbit" class="form-control"
                                        value="{{ old('penerbit', $buku->penerbit) }}" required>
                                    @error('penerbit')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tahun_terbit">Tahun Terbit</label>
                                    <input type="text" name="tahun_terbit" id="tahun_terbit" class="form-control"
                                        value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
                                    @error('tahun_terbit')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="total_buku">Total Buku</label>
                                    <input type="number" name="total_buku" id="total_buku" class="form-control"
                                        value="{{ old('total_buku', $buku->total_buku ?? 0) }}" required>
                                    <small class="form-text text-muted">Jumlah keseluruhan buku. Stok buku akan otomatis
                                        diperbarui.</small>
                                    @error('total_buku')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="harga_buku">Harga Buku</label>
                                    <input type="text" name="harga_buku" id="harga_buku" class="form-control"
                                        value="{{ old('harga_buku', $buku->harga_buku ?? 0) }}" placeholder="0">
                                    <small class="form-text text-muted">Harga buku untuk perhitungan denda (dalam
                                        Rupiah).</small>
                                    @error('harga_buku')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="foto">Foto Sampul</label>
                                    <input type="file" name="foto" id="foto" class="form-control"
                                        onchange="tampilkanPratinjauGambar(event)">
                                    <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB.
                                        Kosongkan jika tidak ingin mengubah foto sampul.</small>
                                    @error('foto')
                                        <div class="custom-alert" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                                </path>
                                            </svg>
                                            <p>{{ $message }}</p>
                                        </div>
                                    @enderror

                                    {{-- Sampul saat ini --}}
                                    <div class="mt-3" style="margin-bottom: 10px;">
                                        <label>Sampul Saat Ini:</label>
                                        <div
                                            style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; display: inline-block;">
                                            @if ($buku->foto)
                                                <img src="{{ asset('assets/img/buku/' . $buku->foto) }}"
                                                    alt="{{ $buku->judul }}"
                                                    style="max-width: 150px; max-height: 200px;">
                                            @else
                                                <img src="{{ asset('assets/img/default_buku.png') }}"
                                                    alt="Default Book Cover" style="max-width: 150px; max-height: 200px;">
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Preview sampul --}}
                                    <div class="mt-3" id="preview-container" style="display: none;">
                                        <label for="preview">Preview Sampul Baru:</label>
                                        <div
                                            style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; display: inline-block;">
                                            <img id="preview" src="#" alt="Preview Sampul"
                                                style="max-width: 150px; max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="deskripsi">Deskripsi Buku</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="custom-alert" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z">
                                        </path>
                                    </svg>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mt-4 text-end">
                            @if (isset($ref) && $ref == 'kategori' && isset($kategori_id))
                                <a href="{{ route('kategori.detail', ['id' => $kategori_id, 'page' => $page ?? '', 'search' => $search ?? '']) }}"
                                    class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back"></i> Kembali ke Kategori
                                </a>
                            @else
                                <a href="{{ route('buku.index', ['page' => $page ?? '', 'search' => $search ?? '', 'kategori' => $kategoriFilter ?? '', 'status' => $status ?? '']) }}"
                                    class="btn btn-secondary me-2">
                                    <i class="bx bx-arrow-back"></i> Kembali
                                </a>
                            @endif
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        // Tunggu halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Format input harga buku secara manual tanpa library
            var inputHargaBuku = document.getElementById('harga_buku');

            // Fungsi untuk format angka ke Rupiah
            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                // Tambahkan titik jika ada ribuan
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
            }

            // Event listener untuk format saat mengetik
            inputHargaBuku.addEventListener('keyup', function(e) {
                // Format input dengan prefix Rupiah
                inputHargaBuku.value = formatRupiah(this.value, 'Rp ');
            });

            // Format nilai awal jika ada (untuk edit mode)
            if (inputHargaBuku.value && inputHargaBuku.value !== '0') {
                inputHargaBuku.value = formatRupiah(inputHargaBuku.value, 'Rp ');
            }

            // Inisialisasi dropdown kategori dengan fitur pencarian
            var pilihanKategori = new Choices('#kategori_id', {
                removeItemButton: true, // Tombol hapus kategori terpilih
                placeholder: true, // Gunakan placeholder
                placeholderValue: 'Pilih kategori…', // Teks placeholder
                shouldSort: false, // Urutan sesuai HTML asli
                searchEnabled: true, // Fitur pencarian aktif
                itemSelectText: '' // Hilangkan teks "Press to select"
            });

            // Batasi input tahun terbit hanya 4 digit
            var inputTahunTerbit = document.getElementById('tahun_terbit');
            inputTahunTerbit.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 4) {
                    this.value = this.value.slice(0, 4);
                }
            });
        });

        // Fungsi untuk menampilkan pratinjau gambar sampul buku
        function tampilkanPratinjauGambar(event) {
            var berkasFoto = event.target.files[0];
            var gambarPratinjau = document.getElementById('preview');
            var wadahPratinjau = document.getElementById('preview-container');

            if (berkasFoto) {
                var pembacaBerkas = new FileReader();
                pembacaBerkas.onload = function(e) {
                    gambarPratinjau.src = e.target.result;
                    wadahPratinjau.style.display = 'block';
                }
                pembacaBerkas.readAsDataURL(berkasFoto);
            } else {
                wadahPratinjau.style.display = 'none';
            }
        }
    </script>
@endsection
