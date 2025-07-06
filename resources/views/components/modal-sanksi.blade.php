<!-- Modal Sanksi untuk Konfirmasi Pengembalian -->
<div class="modal fade" id="sanksiModal" tabindex="-1" aria-labelledby="sanksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary" style="color: white;">
                <h5 class="modal-title" id="sanksiModalLabel">
                    <i class="fas fa-book-open me-2"></i> Konfirmasi Pengembalian Buku
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
                                    <i class="fas fa-info-circle me-2"></i> Informasi Peminjaman
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Buku:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalBukuJudul"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Nama Peminjam:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalPeminjam"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Tanggal Pinjam:</strong></div>
                                    <div class="col-sm-8 col-12" id="modalTanggalPinjam"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 col-12"><strong>Tanggal Kembali:</strong></div>
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
                                    <i class="fas fa-clock"></i> Status Keterlambatan
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
                                <i class="fas fa-book me-2"></i> Kondisi Buku
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
                                <h6 class="alert-heading" style="font-size: 15px">
                                    <i class="fas fa-info-circle"></i> Ketentuan Sanksi:
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
                                <i class="fas fa-calculator me-2"></i> Rincian Denda
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
<link rel="stylesheet" href="{{ asset('assets/css/modal-sanksi.css') }}">
<script src="{{ asset('assets/js/modal-sanksi.js') }}"></script>
