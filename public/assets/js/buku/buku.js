/**
 * JavaScript untuk manajemen halaman buku
 * Digunakan untuk halaman: index, detail, tambah, edit
 *
 * Fungsi utama:
 * - Format input harga buku ke Rupiah
 * - Dropdown kategori dengan pencarian
 * - Preview gambar sampul buku
 * - Validasi input tahun terbit
 */

// Tunggu halaman selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi semua fungsi
    inisialisasiFormatHargaBuku();
    inisialisasiDropdownKategori();
    inisialisasiValidasiTahunTerbit();
    inisialisasiFormatHargaDisplay();
    inisialisasiModalHapus();
    inisialisasiTombolEkspor();
    inisialisasiFormSubmit();
});

/**
 * Inisialisasi format input harga buku
 * Digunakan untuk: tambah.blade.php, edit.blade.php
 */
function inisialisasiFormatHargaBuku() {
    var inputHargaBuku = document.getElementById('harga_buku');

    // Jika tidak ada input harga buku, keluar dari fungsi
    if (!inputHargaBuku) return;

    // Event listener untuk format saat mengetik
    inputHargaBuku.addEventListener('input', function(e) {
        // Format input dengan prefix Rupiah
        this.value = formatRupiah(this.value, 'Rp ');
    });

    // Format nilai awal jika ada (untuk edit mode)
    if (inputHargaBuku.value && inputHargaBuku.value !== '0') {
        inputHargaBuku.value = formatRupiah(inputHargaBuku.value, 'Rp ');
    }
}

/**
 * Inisialisasi form submit handler
 * Digunakan untuk: tambah.blade.php, edit.blade.php
 * Mengkonversi harga dari format Rupiah ke integer sebelum submit
 */
function inisialisasiFormSubmit() {
    var formTambah = document.getElementById('formTambahBuku');
    var formEdit = document.getElementById('formEditBuku');

    // Handler untuk form tambah buku
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            konversiHargaSebelumSubmit();
        });
    }

    // Handler untuk form edit buku
    if (formEdit) {
        formEdit.addEventListener('submit', function(e) {
            konversiHargaSebelumSubmit();
        });
    }
}

/**
 * Fungsi untuk mengkonversi harga dari format Rupiah ke integer
 * Digunakan untuk: tambah.blade.php, edit.blade.php
 * Dipanggil sebelum form submit untuk memastikan backend menerima angka integer
 */
function konversiHargaSebelumSubmit() {
    var inputHargaBuku = document.getElementById('harga_buku');

    if (inputHargaBuku && inputHargaBuku.value) {
        // Simpan nilai asli untuk logging
        var nilaiAsli = inputHargaBuku.value;

        // Hapus semua karakter non-digit (Rp, spasi, titik)
        var nilaiAngka = inputHargaBuku.value.replace(/[^0-9]/g, '');

        // Jika tidak ada angka sama sekali, set ke 0
        if (nilaiAngka === '') {
            nilaiAngka = '0';
        }

        // Set nilai input ke integer murni
        inputHargaBuku.value = nilaiAngka;
    }
}

/**
 * Inisialisasi dropdown kategori dengan fitur pencarian
 * Digunakan di halaman: tambah.blade.php, edit.blade.php
 */
function inisialisasiDropdownKategori() {
    var selectKategori = document.getElementById('kategori_id');

    // Jika tidak ada dropdown kategori, keluar dari fungsi
    if (!selectKategori) return;

    // Inisialisasi dropdown kategori dengan fitur pencarian
    var pilihanKategori = new Choices('#kategori_id', {
        removeItemButton: true,                 // Tombol hapus kategori terpilih
        placeholder: true,                      // Gunakan placeholder
        placeholderValue: 'Pilih kategori…',    // Teks placeholder
        shouldSort: false,                      // Urutan sesuai HTML asli
        searchEnabled: true,                    // Fitur pencarian aktif
        itemSelectText: ''                      // Hilangkan teks "Press to select"
    });
}

/**
 * Inisialisasi validasi input tahun terbit
 * Digunakan di halaman: tambah.blade.php, edit.blade.php
 */
function inisialisasiValidasiTahunTerbit() {
    var inputTahunTerbit = document.getElementById('tahun_terbit');

    // Jika tidak ada input tahun terbit, keluar dari fungsi
    if (!inputTahunTerbit) return;

    // Batasi input tahun terbit hanya 4 digit
    inputTahunTerbit.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
    });
}

/**
 * Inisialisasi format harga untuk tampilan
 * Digunakan di halaman: index.blade.php, detail.blade.php
 */
function inisialisasiFormatHargaDisplay() {
    // Format semua elemen dengan class 'harga-rupiah' ke format Rupiah
    var elemenHarga = document.querySelectorAll('.harga-rupiah');

    elemenHarga.forEach(function(elemen) {
        var nilaiHarga = elemen.textContent || elemen.innerText;
        if (nilaiHarga && !isNaN(nilaiHarga)) {
            elemen.textContent = formatRupiah(nilaiHarga, 'Rp ');
        }
    });
}

/**
 * Inisialisasi modal konfirmasi hapus
 * Digunakan di halaman: index.blade.php
 */
function inisialisasiModalHapus() {
    // Cari semua tombol hapus
    var tombolHapus = document.querySelectorAll('.delete-btn');

    tombolHapus.forEach(function(tombol) {
        tombol.addEventListener('click', function() {
            var actionUrl = this.getAttribute('data-action');
            var formHapus = document.getElementById('delete-form');

            if (formHapus && actionUrl) {
                formHapus.action = actionUrl;
            }
        });
    });
}

/**
 * Inisialisasi tombol ekspor
 * Digunakan di halaman: index.blade.php
 */
function inisialisasiTombolEkspor() {
    var tombolEksporExcel = document.getElementById('exportExcel');
    var tombolEksporWord = document.getElementById('exportWord');

    if (tombolEksporExcel) {
        tombolEksporExcel.addEventListener('click', function() {
            eksporKeExcel();
        });
    }

    if (tombolEksporWord) {
        tombolEksporWord.addEventListener('click', function() {
            eksporKeWord();
        });
    }
}

/**
 * Fungsi untuk mendapatkan semua filter dari URL saat ini
 * Digunakan di halaman: index.blade.php
 * @returns {object} - Objek berisi parameter filter
 */
function dapatkanFilterSaatIni() {
    var urlParams = new URLSearchParams(window.location.search);
    return {
        kategori: urlParams.get('kategori') || '',
        status: urlParams.get('status') || '',
        search: urlParams.get('search') || ''
    };
}

/**
 * Fungsi untuk mengambil semua data buku dari server
 * Digunakan di halaman: index.blade.php (untuk ekspor)
 * @returns {Promise} - Promise yang mengembalikan data buku
 */
function ambilSemuaDataBuku() {
    return new Promise(function(resolve, reject) {
        // Tampilkan indikator loading
        document.body.insertAdjacentHTML('beforeend',
            '<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">' +
            '<div style="background: white; padding: 20px; border-radius: 5px;">' +
            '<i class="bx bx-loader bx-spin" style="font-size: 30px;"></i> Memuat data buku...</div></div>'
        );

        // Dapatkan filter saat ini
        var filter = dapatkanFilterSaatIni();

        // Buat request AJAX ke endpoint ekspor
        var xhr = new XMLHttpRequest();
        var url = '/buku/export/all?' + new URLSearchParams(filter).toString();
        
        xhr.open('GET', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function() {            
            // Hapus indikator loading
            var loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }

            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    
                    if (response.success && response.data) {
                        resolve(response.data);
                    } else {
                        reject("Gagal mengambil data buku");
                    }
                } catch (e) {
                    reject("Error parsing response: " + e.message);
                }
            } else {
                reject("Error fetching data: " + xhr.statusText);
            }
        };

        xhr.onerror = function() {            
            // Hapus indikator loading
            var loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
            reject("Error dalam request data buku");
        };

        xhr.send();
    });
}

/**
 * Fungsi untuk ekspor data buku ke Excel
 * Digunakan di halaman: index.blade.php
 * Termasuk kolom: No, Kode Buku, Judul, Pengarang, Penerbit, Tahun Terbit, Kategori, Total Buku, Stok, Harga Buku, Status, Deskripsi
 */
function eksporKeExcel() {
    // Pastikan library XLSX tersedia
    if (typeof XLSX === 'undefined') {
        alert("Library XLSX tidak ditemukan. Pastikan sudah dimuat.");
        return;
    }

    ambilSemuaDataBuku().then(function(semuaBuku) {
        // Buat data worksheet
        var data = [];

        // Tambahkan baris header
        var headers = ['No', 'Kode Buku', 'Judul', 'Pengarang', 'Penerbit', 'Tahun Terbit',
            'Kategori', 'Total Buku', 'Stok', 'Harga Buku', 'Status', 'Deskripsi'];
        data.push(headers);

        // Tambahkan baris data
        semuaBuku.forEach(function(buku, index) {
            var hargaBuku = (buku.harga_buku != null && buku.harga_buku !== undefined) 
                ? formatRupiah(buku.harga_buku.toString(), 'Rp ') 
                : 'Rp 0';
                
            var row = [
                index + 1,
                buku.kode_buku,
                buku.judul,
                buku.pengarang,
                buku.penerbit,
                buku.tahun_terbit,
                buku.kategori,
                buku.total_buku.toString(),
                buku.stok_buku.toString(),
                hargaBuku,
                buku.status,
                buku.deskripsi
            ];
            data.push(row);
        });

        // Buat worksheet
        var ws = XLSX.utils.aoa_to_sheet(data);

        // Buat workbook dan tambahkan worksheet
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Daftar Buku");

        // Generate nama file dengan tanggal saat ini
        var namaFile = 'Daftar_Buku_' + new Date().toISOString().slice(0, 10).replace(/-/g, '-') + '.xlsx';

        // Ekspor ke file Excel
        XLSX.writeFile(wb, namaFile);
    }).catch(function(error) {
        alert("Terjadi kesalahan saat mengekspor data: " + error);
    });
}

/**
 * Fungsi untuk ekspor data buku ke Word
 * Digunakan di halaman: index.blade.php
 * Termasuk kolom: No, Kode Buku, Judul, Pengarang, Penerbit, Tahun Terbit, Kategori, Total Buku, Stok, Harga Buku, Status, Deskripsi
 */
function eksporKeWord() {
    ambilSemuaDataBuku().then(function(semuaBuku) {
        // Buat konten HTML untuk dokumen Word
        var htmlContent =
            '<html xmlns:o="urn:schemas-microsoft-com:office:office"' +
            ' xmlns:w="urn:schemas-microsoft-com:office:word"' +
            ' xmlns="http://www.w3.org/TR/REC-html40">' +
            '<head>' +
            '<meta charset="utf-8">' +
            '<title>Daftar Buku Perpustakaan</title>' +
            '<!--[if gte mso 9]>' +
            '<xml>' +
            '<w:WordDocument>' +
            '<w:View>Print</w:View>' +
            '<w:Zoom>90</w:Zoom>' +
            '<w:Orientation>Landscape</w:Orientation>' +
            '</w:WordDocument>' +
            '</xml>' +
            '<![endif]-->' +
            '<style>' +
            '@page {' +
            'size: A4 landscape;' +
            'margin: 0.5in;' +
            '}' +
            'body {' +
            'font-family: Arial, sans-serif;' +
            'font-size: 10px;' +
            'margin: 0;' +
            'padding: 0;' +
            '}' +
            '.header {' +
            'text-align: center;' +
            'margin-bottom: 15px;' +
            '}' +
            '.header h2 {' +
            'font-size: 14px;' +
            'margin: 0 0 5px 0;' +
            '}' +
            '.date {' +
            'text-align: center;' +
            'margin-bottom: 15px;' +
            'color: #666;' +
            'font-size: 9px;' +
            '}' +
            'table {' +
            'border-collapse: collapse;' +
            'width: 100%;' +
            'font-size: 8px;' +
            'table-layout: fixed;' +
            '}' +
            'th, td {' +
            'border: 1px solid #ddd;' +
            'padding: 4px;' +
            'text-align: left;' +
            'word-wrap: break-word;' +
            'overflow-wrap: break-word;' +
            'vertical-align: top;' +
            '}' +
            'th {' +
            'background-color: #f2f2f2;' +
            'font-weight: bold;' +
            'font-size: 9px;' +
            '}' +
            '.text-center { text-align: center; }' +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="header">' +
            '<h2>Daftar Buku Perpustakaan</h2>' +
            '</div>' +
            '<div class="date">' +
            '<p>Data per tanggal ' + new Date().toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}) + '</p>' +
            '</div>' +
            '<table>' +
            '<thead>' +
            '<tr>' +
            '<th style="width: 3%;">No</th>' +
            '<th style="width: 7%;">Kode Buku</th>' +
            '<th style="width: 15%;">Judul</th>' +
            '<th style="width: 10%;">Pengarang</th>' +
            '<th style="width: 10%;">Penerbit</th>' +
            '<th style="width: 5%;">Tahun Terbit</th>' +
            '<th style="width: 10%;">Kategori</th>' +
            '<th style="width: 5%;">Total Buku</th>' +
            '<th style="width: 5%;">Stok Buku</th>' +
            '<th style="width: 8%;">Harga Buku</th>' +
            '<th style="width: 5%;">Status</th>' +
            '<th style="width: 17%;">Deskripsi</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>';

        // Tambahkan baris data dari response API
        semuaBuku.forEach(function(buku, index) {
            var hargaBuku = (buku.harga_buku != null && buku.harga_buku !== undefined) 
                ? formatRupiah(buku.harga_buku.toString(), 'Rp ') 
                : 'Rp 0';
                
            htmlContent +=
                '<tr>' +
                '<td style="text-align: center;">' + (index + 1) + '</td>' +
                '<td>' + buku.kode_buku + '</td>' +
                '<td>' + buku.judul + '</td>' +
                '<td>' + buku.pengarang + '</td>' +
                '<td>' + buku.penerbit + '</td>' +
                '<td>' + buku.tahun_terbit + '</td>' +
                '<td>' + buku.kategori + '</td>' +
                '<td>' + buku.total_buku + '</td>' +
                '<td>' + buku.stok_buku + '</td>' +
                '<td>' + hargaBuku + '</td>' +
                '<td>' + buku.status + '</td>' +
                '<td>' + buku.deskripsi + '</td>' +
                '</tr>';
        });

        htmlContent +=
            '</tbody>' +
            '</table>' +
            '</body>' +
            '</html>';

        // Buat blob dan download
        var blob = new Blob([htmlContent], {
            type: 'application/msword'
        });

        var namaFile = 'Daftar_Buku_' + new Date().toISOString().slice(0, 10).replace(/-/g, '-') + '.doc';

        // Buat link download dan klik otomatis
        var linkDownload = document.createElement('a');
        linkDownload.href = URL.createObjectURL(blob);
        linkDownload.download = namaFile;
        document.body.appendChild(linkDownload);
        linkDownload.click();
        document.body.removeChild(linkDownload);
        URL.revokeObjectURL(linkDownload.href);
    }).catch(function(error) {
        alert("Terjadi kesalahan saat mengekspor data: " + error);
    });
}

/**
 * Fungsi untuk format angka ke format Rupiah
 * Digunakan di halaman: tambah.blade.php, edit.blade.php, index.blade.php, detail.blade.php
 * @param {string|number} angka - Angka yang akan diformat
 * @param {string} prefix - Prefix yang akan ditambahkan (opsional)
 * @returns {string} - Angka yang sudah diformat ke Rupiah
 */
function formatRupiah(angka, prefix) {
    var number_string = angka.toString().replace(/[^,\d]/g, ''),
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
    return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
}

/**
 * Fungsi untuk menampilkan pratinjau gambar sampul buku
 * Digunakan di halaman: tambah.blade.php, edit.blade.php
 * @param {Event} event - Event dari input file
 */
function tampilkanPratinjauGambar(event) {
    var berkasFoto = event.target.files[0];
    var gambarPratinjau = document.getElementById('preview');
    var wadahPratinjau = document.getElementById('preview-container');

    // Jika tidak ada elemen preview, keluar dari fungsi
    if (!gambarPratinjau || !wadahPratinjau) return;

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

/**
 * Fungsi untuk konfirmasi hapus buku
 * Digunakan di halaman: index.blade.php
 * @param {string} judulBuku - Judul buku yang akan dihapus
 * @returns {boolean} - True jika user konfirmasi, false jika batal
 */
function konfirmasiHapusBuku(judulBuku) {
    return confirm('Apakah Anda yakin ingin menghapus buku "' + judulBuku + '"?\nTindakan ini tidak dapat dibatalkan.');
}

/**
 * Fungsi untuk toggle detail buku
 * Digunakan di halaman: index.blade.php
 * @param {number} bukuId - ID buku yang akan di-toggle
 */
function toggleDetailBuku(bukuId) {
    var detailRow = document.getElementById('detail-' + bukuId);
    if (detailRow) {
        if (detailRow.style.display === 'none' || detailRow.style.display === '') {
            detailRow.style.display = 'table-row';
        } else {
            detailRow.style.display = 'none';
        }
    }
}

/**
 * Fungsi untuk pencarian buku dengan AJAX (jika diperlukan)
 * Digunakan di halaman: index.blade.php
 * @param {string} kataPencarian - Kata kunci pencarian
 */
function cariBuku(kataPencarian) {
    // Implementasi pencarian AJAX bisa ditambahkan di sini
}

/**
 * Fungsi untuk filter buku berdasarkan kategori
 * Digunakan di halaman: index.blade.php
 * @param {number} kategoriId - ID kategori untuk filter
 */
function filterBerdasarkanKategori(kategoriId) {
    // Implementasi filter berdasarkan kategori bisa ditambahkan di sini
}
