<!-- Modal Sanksi untuk Konfirmasi Pengembalian -->
<div class="modal fade" id="sanksiModal" tabindex="-1" aria-labelledby="sanksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sanksiModalLabel">
                    <i class="fas fa-book-open me-2"></i>Konfirmasi Pengembalian Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Informasi Peminjaman -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-6 col-md-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Peminjaman
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Buku:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalBukuJudul"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Peminjam:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalPeminjam"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Tgl Pinjam:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalTanggalPinjam"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Tgl Kembali:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalTanggalKembali"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 col-12"><strong>Harga Buku:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalHargaBuku"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-clock me-2"></i>Status Keterlambatan
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div id="statusKeterlambatan"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="sanksiForm" method="POST" action="{{ route('sanksi.proses-pengembalian') }}">
                    @csrf
                    <!-- Kondisi Buku -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 text-primary">
                                <i class="fas fa-book me-2"></i>Kondisi Buku
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label for="kondisiBuku" class="form-label fw-bold">Kondisi Buku saat
                                    Dikembalikan:</label>
                                <select name="kondisi_buku" id="kondisiBuku" class="form-select form-select-lg"
                                    required>
                                    <option value="">Pilih kondisi buku...</option>
                                    <option value="baik">
                                        ✓ Tidak Rusak dan Tidak Hilang
                                    </option>
                                    <option value="rusak_hilang">
                                        ⚠ Rusak Parah atau Hilang
                                    </option>
                                </select>
                            </div>
                            <div class="alert alert-info border-0 shadow-sm">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>Ketentuan Sanksi:
                                </h6>
                                <div class="row">
                                    <div class="col-lg-6 col-md-12">
                                        <ul class="mb-0 small">
                                            <li><strong>Tidak Terlambat + Tidak Rusak:</strong> Tidak ada sanksi</li>
                                            <li><strong>Tidak Terlambat + Rusak/Hilang:</strong> Sanksi sesuai harga
                                                buku</li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-12">
                                        <ul class="mb-0 small">
                                            <li><strong>Terlambat + Tidak Rusak:</strong> Sanksi Rp 1.000/hari
                                                keterlambatan</li>
                                            <li><strong>Terlambat + Rusak/Hilang:</strong> Sanksi sesuai harga buku
                                                (tanpa denda keterlambatan)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Denda -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 text-primary">
                                <i class="fas fa-calculator me-2"></i>Rincian Denda
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div id="rincianDenda">
                                <div class="row mb-2">
                                    <div class="col-8">Denda Keterlambatan:</div>
                                    <div class="col-4 text-end fw-bold" id="dendaKeterlambatan">Rp 0</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-8">Denda Kerusakan/Kehilangan:</div>
                                    <div class="col-4 text-end fw-bold" id="dendaKerusakan">Rp 0</div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-8"><strong>Total Denda:</strong></div>
                                    <div class="col-4 text-end">
                                        <span class="badge bg-danger fs-6" id="totalDenda">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-bold d-block mb-2">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                            placeholder="Masukkan keterangan tambahan jika diperlukan..."></textarea>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="hidden" name="peminjaman_id" id="peminjamanId">
                    <input type="hidden" name="hari_terlambat" id="hariTerlambat">
                    <input type="hidden" name="denda_keterlambatan" id="dendaKeterlambatanValue">
                    <input type="hidden" name="denda_kerusakan" id="dendaKerusakanValue">
                    <input type="hidden" name="total_denda" id="totalDendaValue">
                    <input type="hidden" name="harga_buku" id="hargaBukuValue">
                </form>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="submit" class="btn btn-success btn-lg" id="konfirmasiPengembalian">
                    <i class="fas fa-check me-2"></i>Konfirmasi Pengembalian
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom CSS untuk Modal Sanksi - Responsive Design */
    #sanksiModal .modal-dialog {
        max-width: 95vw;
        width: 100%;
        margin: 0.5rem auto;
    }

    #sanksiModal .modal-content {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        max-height: 95vh;
        overflow: hidden;
    }

    #sanksiModal .modal-header {
        border-bottom: none;
        border-radius: 10px 10px 0 0;
        flex-shrink: 0;
    }

    #sanksiModal .modal-body {
        overflow-y: auto;
        max-height: calc(95vh - 140px);
        flex: 1;
    }

    #sanksiModal .modal-footer {
        border-top: none;
        border-radius: 0 0 10px 10px;
        flex-shrink: 0;
    }

    #sanksiModal .card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    #sanksiModal .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    #sanksiModal .card-header {
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }

    #sanksiModal .form-select,
    #sanksiModal .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    #sanksiModal .form-select:focus,
    #sanksiModal .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    #sanksiModal .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    #sanksiModal .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    #sanksiModal .alert {
        border-radius: 6px;
        border: none;
    }

    #sanksiModal .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
    }

    #sanksiModal .text-primary {
        color: #007bff !important;
    }

    #sanksiModal .bg-primary {
        background-color: #007bff !important;
    }

    #sanksiModal .bg-light {
        background-color: #f8f9fa !important;
    }

    #sanksiModal .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Keterangan Section */
    #sanksiModal .mb-3 {
        margin-bottom: 1.5rem;
    }

    #sanksiModal #keterangan {
        resize: vertical;
        min-height: 80px;
        background-color: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 0.75rem;
        font-size: 0.9rem;
        line-height: 1.5;
        transition: all 0.3s ease;
        width: 100%;
        display: block;
    }

    #sanksiModal #keterangan:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    #sanksiModal #keterangan::placeholder {
        color: #6c757d;
        opacity: 0.7;
        font-style: italic;
    }

    #sanksiModal .form-label.fw-bold {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
        width: 100%;
    }

    #sanksiModal .form-label.fw-bold.d-block.mb-2 {
        margin-bottom: 0.5rem !important;
        clear: both;
    }

    /* Status Keterlambatan */
    #sanksiModal #statusKeterlambatan .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }

    #sanksiModal #statusKeterlambatan .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    /* Large Screen (Desktop) */
    @media (min-width: 1200px) {
        #sanksiModal .modal-dialog {
            max-width: 1140px;
        }
    }

    /* Medium Screen (Tablet) */
    @media (min-width: 768px) and (max-width: 1199px) {
        #sanksiModal .modal-dialog {
            max-width: 90vw;
        }

        #sanksiModal .modal-body {
            max-height: calc(90vh - 140px);
        }
    }

    /* Small Screen (Mobile) */
    @media (max-width: 767px) {
        #sanksiModal .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100vw - 0.5rem);
            width: calc(100vw - 0.5rem);
        }

        #sanksiModal .modal-content {
            max-height: calc(100vh - 0.5rem);
        }

        #sanksiModal .modal-body {
            max-height: calc(100vh - 120px);
            padding: 1rem;
        }

        #sanksiModal .modal-header,
        #sanksiModal .modal-footer {
            padding: 0.75rem 1rem;
        }

        #sanksiModal .card-body {
            padding: 1rem;
        }

        #sanksiModal .row .col-sm-4,
        #sanksiModal .row .col-sm-8 {
            padding: 0.25rem 0.75rem;
        }

        #sanksiModal .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        #sanksiModal .modal-footer {
            flex-direction: column-reverse;
        }

        #sanksiModal .modal-footer .btn {
            margin: 0.25rem 0;
        }

        #sanksiModal .alert ul {
            font-size: 0.8rem;
        }

        /* Compact card headers on mobile */
        #sanksiModal .card-header h6 {
            font-size: 0.9rem;
        }

        /* Better spacing for mobile */
        #sanksiModal .mb-4 {
            margin-bottom: 1rem !important;
        }

        #sanksiModal .mb-3 {
            margin-bottom: 0.75rem !important;
        }

        /* Keterangan mobile optimization */
        #sanksiModal .mb-3 {
            margin-bottom: 1rem !important;
        }

        #sanksiModal #keterangan {
            font-size: 0.85rem;
            min-height: 70px;
            padding: 0.5rem;
        }

        #sanksiModal .form-label.fw-bold {
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }

        #sanksiModal .form-label.fw-bold.d-block.mb-2 {
            margin-bottom: 0.4rem !important;
        }
    }

    /* Very Small Screen (Small Mobile) */
    @media (max-width: 480px) {
        #sanksiModal .modal-dialog {
            margin: 0.125rem;
            max-width: calc(100vw - 0.25rem);
            width: calc(100vw - 0.25rem);
        }

        #sanksiModal .modal-body {
            padding: 0.75rem;
        }

        #sanksiModal .card-body {
            padding: 0.75rem;
        }

        #sanksiModal .form-select-lg,
        #sanksiModal .btn-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
    }

    /* Landscape orientation optimization */
    @media (max-height: 600px) and (orientation: landscape) {
        #sanksiModal .modal-content {
            max-height: 95vh;
        }

        #sanksiModal .modal-body {
            max-height: calc(95vh - 100px);
        }

        #sanksiModal .modal-header,
        #sanksiModal .modal-footer {
            padding: 0.5rem 1rem;
        }
    }

    /* Scrollbar styling */
    #sanksiModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #sanksiModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    #sanksiModal .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    #sanksiModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sanksiModal = document.getElementById('sanksiModal');
        const kondisiBukuSelect = document.getElementById('kondisiBuku');
        const konfirmasiBtn = document.getElementById('konfirmasiPengembalian');

        // Data sementara untuk perhitungan
        let currentData = {
            peminjaman_id: null,
            hari_terlambat: 0,
            harga_buku: 0,
            denda_per_hari: 1000 // Rp 1000 per hari
        };

        // Event listener untuk perubahan kondisi buku
        kondisiBukuSelect.addEventListener('change', function() {
            hitungDenda();
        });

        // Fungsi untuk menghitung denda
        function hitungDenda() {
            const kondisiBuku = kondisiBukuSelect.value;
            const hariTerlambat = currentData.hari_terlambat;
            const hargaBuku = Math.floor(currentData.harga_buku); // Pastikan integer
            const dendaPerHari = currentData.denda_per_hari;

            let dendaKeterlambatan = 0;
            let dendaKerusakan = 0;
            let totalDenda = 0;

            // Logika sanksi berdasarkan ketentuan baru:
            if (hariTerlambat > 0) {
                // Jika terlambat
                if (kondisiBuku === 'baik') {
                    // Tidak rusak dan tidak hilang: hanya denda keterlambatan
                    dendaKeterlambatan = hariTerlambat * dendaPerHari;
                    dendaKerusakan = 0;
                } else if (kondisiBuku === 'rusak_hilang') {
                    // Rusak parah atau hilang: hanya denda sesuai harga buku (hapus denda keterlambatan)
                    dendaKeterlambatan = 0;
                    dendaKerusakan = hargaBuku;
                }
            } else {
                // Jika tidak terlambat
                if (kondisiBuku === 'baik') {
                    // Tidak rusak dan tidak hilang: tidak ada sanksi
                    dendaKeterlambatan = 0;
                    dendaKerusakan = 0;
                } else if (kondisiBuku === 'rusak_hilang') {
                    // Rusak parah atau hilang: sanksi sesuai harga buku
                    dendaKeterlambatan = 0;
                    dendaKerusakan = hargaBuku;
                }
            }

            // Total denda
            totalDenda = dendaKeterlambatan + dendaKerusakan;

            // Update tampilan
            document.getElementById('dendaKeterlambatan').textContent = formatRupiah(dendaKeterlambatan);
            document.getElementById('dendaKerusakan').textContent = formatRupiah(dendaKerusakan);
            document.getElementById('totalDenda').textContent = formatRupiah(totalDenda);

            // Update badge color berdasarkan total denda
            const totalDendaElement = document.getElementById('totalDenda');
            if (totalDenda > 0) {
                totalDendaElement.className = 'badge bg-danger fs-6';
            } else {
                totalDendaElement.className = 'badge bg-success fs-6';
            }

            // Update hidden inputs
            document.getElementById('dendaKeterlambatanValue').value = dendaKeterlambatan;
            document.getElementById('dendaKerusakanValue').value = dendaKerusakan;
            document.getElementById('totalDendaValue').value = totalDenda;
        }

        // Fungsi untuk format rupiah
        function formatRupiah(amount) {
            return 'Rp ' + Math.floor(amount).toLocaleString('id-ID');
        }

        // Fungsi untuk menghitung hari terlambat
        function hitungHariTerlambat(tanggalKembali) {
            const today = new Date();
            const returnDate = new Date(tanggalKembali);

            // Set waktu return date ke akhir hari (23:59:59) untuk konsistensi dengan backend
            returnDate.setHours(23, 59, 59, 999);

            // Hanya terlambat jika hari ini melewati akhir hari tanggal kembali
            if (today <= returnDate) {
                return 0; // Tidak terlambat
            }

            // Hitung selisih hari dengan cara yang sama seperti backend
            const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const returnStart = new Date(returnDate.getFullYear(), returnDate.getMonth(), returnDate.getDate());
            const timeDiff = todayStart.getTime() - returnStart.getTime();
            const daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));

            return daysDiff > 0 ? daysDiff : 0;
        }

        // Fungsi untuk menampilkan modal dengan data
        window.showSanksiModal = function(data) {
            // Set data sementara
            currentData = {
                peminjaman_id: data.peminjaman_id,
                hari_terlambat: hitungHariTerlambat(data.tanggal_kembali),
                harga_buku: Math.floor(parseFloat(data.harga_buku)), // Pastikan integer
                denda_per_hari: 1000
            };

            // Populate modal dengan data
            document.getElementById('modalBukuJudul').textContent = data.judul_buku;
            document.getElementById('modalPeminjam').textContent = data.nama_peminjam;
            document.getElementById('modalTanggalPinjam').textContent = formatTanggal(data.tanggal_pinjam);
            document.getElementById('modalTanggalKembali').textContent = formatTanggal(data
                .tanggal_kembali);
            document.getElementById('modalHargaBuku').textContent = formatRupiah(data.harga_buku);

            // Set hidden inputs
            document.getElementById('peminjamanId').value = data.peminjaman_id;
            document.getElementById('hariTerlambat').value = currentData.hari_terlambat;
            document.getElementById('hargaBukuValue').value = currentData.harga_buku;

            // Status keterlambatan
            const statusDiv = document.getElementById('statusKeterlambatan');
            if (currentData.hari_terlambat > 0) {
                statusDiv.innerHTML = `
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <strong>Terlambat ${currentData.hari_terlambat} hari</strong><br>
                            <small>Denda keterlambatan: ${formatRupiah(currentData.hari_terlambat * currentData.denda_per_hari)}</small>
                        </div>
                    </div>
                </div>
            `;
            } else {
                statusDiv.innerHTML = `
                <div class="alert alert-success border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                        <div>
                            <strong>Tidak Terlambat</strong><br>
                            <small>Pengembalian tepat waktu</small>
                        </div>
                    </div>
                </div>
            `;
            }

            // Reset form dan button state
            kondisiBukuSelect.value = '';
            document.getElementById('keterangan').value = '';
            konfirmasiBtn.disabled = false;
            konfirmasiBtn.innerHTML = '<i class="fas fa-check me-2"></i>Konfirmasi Pengembalian';

            // Hapus alert warning jika ada
            const existingAlert = document.querySelector('#sanksiForm .alert-warning');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Hitung denda awal
            hitungDenda();

            // Tampilkan modal
            const modal = new bootstrap.Modal(sanksiModal);
            modal.show();
        };

        // Fungsi format tanggal
        function formatTanggal(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Event listener untuk form submission
        document.getElementById('sanksiForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submit default

            const kondisiBuku = kondisiBukuSelect.value;

            if (!kondisiBuku) {
                // Tampilkan alert yang lebih user-friendly
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning border-0 shadow-sm mt-3';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Silakan pilih kondisi buku terlebih dahulu!
                `;

                // Hapus alert sebelumnya jika ada
                const existingAlert = document.querySelector('#sanksiForm .alert-warning');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Tambahkan alert baru
                this.appendChild(alertDiv);

                // Hapus alert setelah 3 detik
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);

                return;
            }

            // Tampilkan loading state
            konfirmasiBtn.disabled = true;
            konfirmasiBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            // Submit form dengan FormData
            const formData = new FormData(this);

            // Kirim data
            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Tutup modal
                        bootstrap.Modal.getInstance(sanksiModal).hide();

                        // Tampilkan pesan sukses dengan SweetAlert jika tersedia, jika tidak gunakan alert biasa
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message || 'Pengembalian berhasil dikonfirmasi!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert(data.message || 'Pengembalian berhasil dikonfirmasi!');
                            location.reload();
                        }
                    } else {
                        // Reset button state
                        konfirmasiBtn.disabled = false;
                        konfirmasiBtn.innerHTML =
                            '<i class="fas fa-check me-2"></i>Konfirmasi Pengembalian';

                        // Tampilkan error dengan SweetAlert jika tersedia
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + (data.message ||
                                    'Unknown error'),
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Reset button state
                    konfirmasiBtn.disabled = false;
                    konfirmasiBtn.innerHTML =
                        '<i class="fas fa-check me-2"></i>Konfirmasi Pengembalian';

                    // Tampilkan error dengan SweetAlert jika tersedia
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan dalam memproses pengembalian',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Terjadi kesalahan dalam memproses pengembalian');
                    }
                });
        });

        // Event listener untuk tombol konfirmasi (trigger submit)
        konfirmasiBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sanksiForm').dispatchEvent(new Event('submit'));
        });
    });
</script>
