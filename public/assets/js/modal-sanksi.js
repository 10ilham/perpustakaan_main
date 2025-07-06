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

    // Event listener untuk modal events
    sanksiModal.addEventListener('hidden.bs.modal', function() {
        // Reset form ketika modal ditutup
        resetModalForm();
        // Pastikan tidak ada backdrop yang tertinggal dan body tidak ada class modal-open
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        // Hapus semua backdrop yang mungkin tertinggal
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        // Reset z-index jika ada masalah
        sanksiModal.style.zIndex = '';
    });

    // Event listener untuk show modal
    sanksiModal.addEventListener('show.bs.modal', function() {
        // Pastikan tidak ada backdrop lama
        const oldBackdrops = document.querySelectorAll('.modal-backdrop');
        oldBackdrops.forEach(backdrop => backdrop.remove());
    });

    // Event listener untuk tombol batal
    const batalBtn = sanksiModal.querySelector('[data-bs-dismiss="modal"]');
    if (batalBtn) {
        batalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Tutup modal dengan proper cleanup
            const modalInstance = bootstrap.Modal.getInstance(sanksiModal);
            if (modalInstance) {
                modalInstance.hide();
            } else {
                // Fallback manual close
                sanksiModal.classList.remove('show');
                sanksiModal.style.display = 'none';
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
            }
        });
    }

    // Fungsi untuk reset form modal
    function resetModalForm() {
        kondisiBukuSelect.value = '';
        document.getElementById('keterangan').value = '';
        konfirmasiBtn.disabled = false;
        konfirmasiBtn.innerHTML = '<i class="fas fa-check me-2"></i>Konfirmasi Pengembalian';

        // Hapus alert warning jika ada
        const existingAlert = document.querySelector('#sanksiForm .alert-warning');
        if (existingAlert) {
            existingAlert.remove();
        }
    }

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
                    <div>
                        <strong>Terlambat ${currentData.hari_terlambat} hari</strong>
                        <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
                        <br>
                        <small>Denda keterlambatan: ${formatRupiah(currentData.hari_terlambat * currentData.denda_per_hari)}</small>
                    </div>
                </div>
            </div>
        `;
        } else {
            statusDiv.innerHTML = `
            <div class="alert alert-success border-0 shadow-sm">
                <div class="d-flex align-items-center">

                    <div>
                        <strong>Tidak Terlambat</strong>
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                        <br>
                        <small>Pengembalian tepat waktu</small>
                    </div>
                </div>
            </div>
        `;
        }

        // Reset form dan button state
        resetModalForm();

        // Hitung denda awal
        hitungDenda();

        // Pastikan tidak ada modal yang terbuka sebelumnya
        const existingModals = document.querySelectorAll('.modal.show');
        existingModals.forEach(modal => {
            const instance = bootstrap.Modal.getInstance(modal);
            if (instance) instance.hide();
        });

        // Hapus backdrop lama jika ada
        const oldBackdrops = document.querySelectorAll('.modal-backdrop');
        oldBackdrops.forEach(backdrop => backdrop.remove());

        // Tunggu sebentar sebelum membuka modal baru
        setTimeout(() => {
            // Tampilkan modal dengan proper configuration
            const modalInstance = new bootstrap.Modal(sanksiModal, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            modalInstance.show();
        }, 100);
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

    // Event listener untuk tombol konfirmasi (trigger submit)
    konfirmasiBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('sanksiForm').dispatchEvent(new Event('submit'));
    });

    // Prevent form default submission
    document.getElementById('sanksiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleFormSubmission.call(this);
    });

    // Function to handle form submission
    function handleFormSubmission() {
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
        const formData = new FormData(document.getElementById('sanksiForm'));

        // Kirim data
        fetch(document.getElementById('sanksiForm').action, {
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
                    // Tutup modal dengan proper cleanup
                    const modalInstance = bootstrap.Modal.getInstance(sanksiModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Cleanup manual jika diperlukan
                    setTimeout(() => {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => backdrop.remove());
                    }, 300);

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
    }
});
