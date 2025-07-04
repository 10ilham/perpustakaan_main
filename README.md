<div align="center">
  <img src="https://private-user-images.githubusercontent.com/128197332/318203316-29788684-29e3-4d02-b3ad-4fe637ba3923.gif?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3NDcwMzEyMzEsIm5iZiI6MTc0NzAzMDkzMSwicGF0aCI6Ii8xMjgxOTczMzIvMzE4MjAzMzE2LTI5Nzg4Njg0LTI5ZTMtNGQwMi1iM2FkLTRmZTYzN2JhMzkyMy5naWY_WC1BbXotQWxnb3JpdGhtPUFXUzQtSE1BQy1TSEEyNTYmWC1BbXotQ3JlZGVudGlhbD1BS0lBVkNPRFlMU0E1M1BRSzRaQSUyRjIwMjUwNTEyJTJGdXMtZWFzdC0xJTJGczMlMkZhd3M0X3JlcXVlc3QmWC1BbXotRGF0ZT0yMDI1MDUxMlQwNjIyMTFaJlgtQW16LUV4cGlyZXM9MzAwJlgtQW16LVNpZ25hdHVyZT00YjViN2QyNjRkZWRjODY4ODI5MTQ5ZjZlNjgwZDAwYTJmNTBhOTQ0YjA0MGI3OWVjYjVhZmY1NjQ4YTk2NWUyJlgtQW16LVNpZ25lZEhlYWRlcnM9aG9zdCJ9.eWsGvmI9janBzin22nrJUQx4KBU9iFsUg-l8oR0o9uI" width="900" height="100"/>
  <h1>Sistem Informasi Perpustakaan MTSN 6 Garut</h1>
  <p>Aplikasi manajemen perpustakaan modern berbasis Laravel dengan sistem notifikasi email dan QR Code</p>
  
  <p>
    <a href="#fitur"><img src="https://img.shields.io/badge/Fitur-Lengkap-brightgreen" alt="Fitur"></a>
    <a href="#penggunaan"><img src="https://img.shields.io/badge/Status-Aktif-blue" alt="Status"></a>
    <a href="#instalasi"><img src="https://img.shields.io/badge/Laravel-12.x-red" alt="Laravel"></a>
    <a href="#lisensi"><img src="https://img.shields.io/badge/Lisensi-MIT-yellow" alt="License"></a>
    <a href="#qr-code"><img src="https://img.shields.io/badge/QR%20Code-Terintegrasi-orange" alt="QR Code"></a>
  </p>
</div>

## 📚 Tentang Aplikasi

Sistem Informasi Perpustakaan MTSN 6 Garut adalah aplikasi manajemen perpustakaan komprehensif yang dibuat untuk memudahkan pengelolaan koleksi buku, peminjaman, dan pelacakan koleksi perpustakaan. Aplikasi ini dirancang dengan fokus pada pengalaman pengguna yang intuitif dan dilengkapi dengan sistem notifikasi otomatis untuk pengembalian buku.

### ✨ Fitur Utama

-   **Manajemen Buku**
    -   Pencatatan lengkap data buku dengan gambar sampul
    -   Kategorisasi buku multi-kategori
    -   Pelacakan stok buku secara real-time
    -   QR Code untuk peminjaman cepat dengan logo sekolah
    -   Download QR Code untuk dicetak dan ditempelkan pada buku fisik
-   **Sistem Peminjaman**

    -   Peminjaman dan pengembalian dengan antarmuka intuitif
    -   Peminjaman buku melalui scan QR Code
    -   Batasan peminjaman berdasarkan tipe pengguna (siswa/guru/staff)
    -   Pelacakan riwayat peminjaman dengan statistik lengkap
    -   Deteksi otomatis pengembalian terlambat

-   **Manajemen Pengguna**

    -   Akun untuk admin, guru, staff, dan siswa
    -   Profil pengguna yang dapat dikelola
    -   Hak akses berbasis peran
    -   Riwayat aktivitas peminjaman per pengguna

-   **Notifikasi & Pengingat**

    -   Sistem notifikasi email 24 jam berbasis Windows Service
    -   Pengingat otomatis untuk batas pengembalian buku
    -   Notifikasi terlambat untuk buku yang belum dikembalikan
    -   Pengiriman email dengan antrian asinkron

-   **Fitur Administrasi**
    -   Dashboard statistik lengkap dengan grafik interaktif
    -   Laporan peminjaman dan pengembalian real-time
    -   Data buku paling populer dan paling sering dipinjam
    -   Filter dan pencarian data multi-parameter

## 📊 Arsitektur Sistem

Sistem notifikasi perpustakaan menggunakan arsitektur berikut:

```
+-------------------+     +----------------+     +-----------------+
| Laravel Scheduler |---->| Database Queue |---->| Laravel Queue   |
| (Windows Service) |     | (MySQL/MariaDB)|     | Worker          |
+-------------------+     +----------------+     | (Windows Service)|
                                |                +-----------------+
                                |                        |
                                v                        v
                          +-----------+           +-------------+
                          | Peminjaman|           | SMTP Server |
                          | Database  |           | (Email)     |
                          +-----------+           +-------------+
```

## 🔧 Instalasi dan Pengaturan

### Prasyarat

-   PHP 8.1 atau lebih tinggi
-   Composer
-   MySQL/MariaDB
-   Node.js dan NPM
-   Server web (Apache/Nginx)
-   Php Imagick

### Langkah Instalasi

1. Clone repositori ini

    ```bash
    git clone https://github.com/username/perpustakaan-mtsn6.git
    cd perpustakaan-mtsn6
    ```

2. Instal dependensi PHP

    ```bash
    composer install
    ```

3. Instal dependensi JavaScript

    ```bash
    npm install && npm run build
    ```

4. Atur file lingkungan

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. Konfigurasi database dan SMTP di file `.env`

6. Jalankan migrasi database

    ```bash
    php artisan migrate --seed
    ```

7. Konfigurasikan layanan notifikasi 24 jam
    ```bash
    php artisan queue:table
    php artisan migrate
    ```

## 💡 Penggunaan

### Manajemen Buku

-   Tambahkan buku baru melalui menu "Tambah Buku"
-   Atur kategori, jumlah stok, dan detail lainnya
-   Unggah foto sampul buku (opsional)
-   Generate QR code untuk buku secara otomatis dengan logo sekolah terintegrasi
-   Download dan cetak QR code untuk ditempel pada buku fisik

### <a name="qr-code"></a>Teknologi QR Code

-   QR Code dihasilkan menggunakan library SimpleSoftwareIO/simple-qrcode
-   Setiap QR code memiliki logo sekolah di tengahnya
-   Level koreksi kesalahan tinggi (H) memastikan QR code dapat dipindai meski ada kerusakan sebagian
-   QR code langsung mengarahkan ke halaman peminjaman buku yang sesuai
-   Dapat dipindai dengan aplikasi kamera standar atau aplikasi QR scanner
-   Format PNG dengan resolusi tinggi untuk pencetakan berkualitas

### Peminjaman Buku

-   Pindai QR code atau pilih buku dari daftar katalog
-   Isi formulir peminjaman dengan batas waktu pengembalian (maksimal 3 hari)
-   Sistem secara otomatis menurunkan stok buku dan memperbarui status ketersediaan
-   Pantau status peminjaman melalui dashboard interaktif
-   Batasan satu buku per pengguna untuk memastikan pemerataan akses

### Notifikasi & Pengingat

-   Pengingat email dikirim otomatis sebelum batas waktu pengembalian
-   Notifikasi terlambat dikirim untuk buku yang belum dikembalikan
-   Status buku berubah otomatis menjadi "Terlambat" saat melewati batas waktu
-   Antrian email menggunakan Laravel Queue untuk memastikan pengiriman yang andal
-   Service Windows berjalan 24/7 untuk memproses antrian notifikasi

### Pemeliharaan Sistem

-   Pantau log aplikasi di direktori `storage/logs/`
-   Cek status layanan notifikasi Windows Service melalui panel NSSM
-   Ikuti panduan troubleshooting untuk mengatasi masalah umum
-   Dokumentasi lengkap untuk pemeliharaan jangka panjang sistem

## 🔄 Arsitektur Object-Oriented Programming (OOP)

Sistem Perpustakaan ini dibangun menggunakan paradigma Object-Oriented Programming (OOP), mengikuti prinsip-prinsip MVC (Model-View-Controller) dari framework Laravel. Berikut adalah penjelasan komprehensif tentang implementasi OOP dalam sistem ini:

### Prinsip OOP yang Diimplementasikan

#### 1. Encapsulation (Enkapsulasi)

-   **Definisi**: Penyembunyian data internal objek dan hanya mengekspos antarmuka yang diperlukan
-   **Implementasi**:
    -   Penggunaan modifier akses `protected` untuk properti model seperti `$fillable` dan `$table`
    -   Properti sensitif seperti `password` disembunyikan dengan `$hidden` dalam model User
    -   Model-model hanya mengekspos metode publik yang diperlukan untuk operasi yang valid

#### 2. Inheritance (Pewarisan)

-   **Definisi**: Pembuatan kelas baru berdasarkan kelas yang sudah ada
-   **Implementasi**:
    -   Semua model mewarisi kelas dasar `Illuminate\Database\Eloquent\Model`
    -   `User` model mewarisi `Illuminate\Foundation\Auth\User` (Authenticatable)
    -   Controller-controller mewarisi kelas dasar `App\Http\Controllers\Controller`
    -   Middleware-middleware mewarisi `Illuminate\Foundation\Http\Middleware\...`

#### 3. Polymorphism (Polimorfisme)

-   **Definisi**: Kemampuan objek untuk tampil dalam berbagai bentuk
-   **Implementasi**:
    -   Penggunaan interface `MustVerifyEmail` yang diimplementasikan oleh User
    -   Metode-metode yang sama (`user()`) dengan implementasi berbeda di model-model yang berbeda
    -   Method overriding pada controller yang mewarisi perilaku dari controller dasar

#### 4. Abstraction (Abstraksi)

-   **Definisi**: Penyembunyian kompleksitas dengan menyediakan antarmuka sederhana
-   **Implementasi**:
    -   Relasi Eloquent menyembunyikan kompleksitas query database di belakang metode sederhana
    -   Facade Laravel menyediakan antarmuka statis untuk fungsionalitas kompleks
    -   Service Provider yang mengabstraksi logika inisialisasi komponen

#### 5. Association (Asosiasi)

-   **Definisi**: Hubungan antara objek yang independen satu sama lain
-   **Implementasi**:
    -   Relasi one-to-one antara `User` dan tipe pengguna (`AdminModel`, `SiswaModel`, dll)
    -   Relasi one-to-many antara `BukuModel` dan `PeminjamanModel`
    -   Relasi many-to-many antara `BukuModel` dan `KategoriModel`

### Struktur Database "perpustakaan"

Sistem ini dibangun di atas database MySQL/MariaDB bernama "perpustakaan" yang menjadi induk (parent) dari seluruh struktur aplikasi. Database ini terdiri dari beberapa tabel utama yang saling berelasi:

-   **users** - Tabel induk untuk semua pengguna sistem
-   **admin, siswa, guru, staff** - Tabel profil untuk tipe pengguna berbeda
-   **buku** - Tabel untuk data koleksi buku perpustakaan
-   **kategori** - Tabel untuk kategori buku
-   **kategori_buku** - Tabel junction untuk relasi many-to-many antara buku dan kategori
-   **peminjaman** - Tabel untuk transaksi peminjaman buku
-   **sessions, password_reset_tokens, jobs** - Tabel pendukung untuk fungsionalitas sistem

Seluruh model dalam aplikasi berkomunikasi dengan tabel-tabel dalam database ini, menjadikan "perpustakaan" sebagai induk yang menyatukan seluruh komponen sistem.

### Class Diagram Sistem Perpustakaan

Berikut adalah class diagram yang menggambarkan struktur dan relasi antar kelas dalam sistem, termasuk hubungan antara controllers dan models dengan notasi UML yang tepat:

```
┌──────────────────────────────────────────────────────────────────┐
│                    DATABASE PERPUSTAKAAN                         │
└──────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌───────────────────┐       ┌───────────────────┐      ┌──────────────────┐
│    Controller     │       │      Model        │      │       View       │
├───────────────────┤       ├───────────────────┤      ├──────────────────┤
│ Base Controller   │       │  Eloquent Model   │      │ Blade Templates  │
└─────────┬─────────┘       └────────┬──────────┘      └──────────────────┘
          │                          │                           ▲
          ▼                          ▼                           │
┌──────────────────────────────────────────────────────────────────────────┐
│                          MVC Architecture                                │
└──────────────────────────────────────────────────────────────────────────┘


┌───────────────────────┐          ┌─────────────────────────┐
│    AdminController    │          │          User           │
├───────────────────────┤          ├─────────────────────────┤
│ -id: int              │          │ -id: int                │
│ -nama: string         │          │ -nama: string           │
│ -email: string        │          │ -email: string          │
│ -password: string     │          │ -password: string       │
│ -level: enum          │          │ -level: enum            │
├───────────────────────┤          │ -email_verified_at      │
│ +showAdminData()      │<>········│ -created_at: timestamp  │
│ +showProfile()        │          │ -updated_at: timestamp  │
│ +editProfile()        │          ├─────────────────────────┤
│ +updateProfile()      │          │ +admin()                │
└───────────────────────┘          │ +siswa()                │
                                   │ +guru()                 │
                                   │ +staff()                │
                                   └───────────┬─────────────┘
                                               │
                                               │ 1
                                               ▼
                    ┌────────────────────────────────────────────┐
                    │                                            │
                    │                                            │
      ┌─────────────┴──────────────┐           ┌────────────────┴───────────┐
      │       SiswaModel           │           │        AdminModel          │
      ├────────────────────────────┤           ├────────────────────────────┤
      │ -id: int                   │           │ -id: int                   │
      │ -user_id: int              │           │ -user_id: int              │
      │ -nis: string               │           │ -nip: string               │
      │ -kelas: string             │           │ -alamat: string            │
      │ -alamat: string            │           │ -no_telepon: string        │
      │ -tanggal_lahir: date       │           │ -tanggal_lahir: date       │
      │ -no_telepon: string        │           │ -foto: string              │
      │ -foto: string              │           ├────────────────────────────┤
      ├────────────────────────────┤           │ +user()                    │
      │ +showSiswaData()           │           └────────────────────────────┘
      │ +user()                    │
      │ +peminjaman()              │
      └────────────────────────────┘


┌───────────────────────┐         1  ┌─────────────────────────┐        ┌─────────────────────┐
│    BukuController     │<>·········>│       BukuModel         │<·······│  PeminjamanModel    │
├───────────────────────┤            ├─────────────────────────┤        ├─────────────────────┤
│ +index()              │            │ -id: int                │1      *│ -id: int            │
│ +tambah()             │            │ -kode_buku: string      │───────>│ -user_id: int       │
│ +simpan()             │            │ -judul: string          │        │ -buku_id: int       │
│ +detail()             │            │ -pengarang: string      │        │ -no_peminjaman: str │
│ +edit()               │            │ -penerbit: string       │        │ -tanggal_pinjam     │
│ +update()             │            │ -tahun_terbit: int      │        │ -tanggal_kembali    │
│ +hapus()              │            │ -deskripsi: text        │        │ -status: string     │
│ +downloadQrCode()     │            │ -foto: string           │        │ -is_terlambat: bool │
│ +pinjamBuku()         │            │ -stok_buku: int         │        ├─────────────────────┤
└───────────────────────┘            │ -total_buku: int        │        │ +user()             │
                                     │ -status: string         │        │ +buku()             │
                                     ├─────────────────────────┤        └──────────△──────────┘
┌───────────────────────┐            │ +kategori()             │                   │
│  KategoriController   │<>·········>│ +peminjaman()           │                   │
├───────────────────────┤            └───────────┬─────────────┘                   │
│ +index()              │                      * │                                 │
│ +detail()             │                        │ *                               │
│ +tambah()             │              ┌─────────▼────────────┐                    │
│ +simpan()             │              │    KategoriModel     │                    │
│ +edit()               │              ├────────────────────────┤                  │
│ +update()             │              │ -id: int               │                  │
│ +hapus()              │              │ -nama: string          │                  │
└───────────────────────┘              │ -deskripsi: text       │                  │
                                       ├────────────────────────┤                  │
                                       │ +buku()                │                  │ 1
                                       └────────────────────────┘                  │
                                                                                   │
                                                                                   │
┌───────────────────────────┐                                                      │
│  PeminjamanController     │<>·····················································┘
├───────────────────────────┤
│ +index()                  │
│ +updateLateStatus()       │
│ +formPinjam()             │
│ +pinjamBuku()             │
│ +detail()                 │
│ +cekKeterlambatan()       │
│ +hitungHariTerlambat()    │
│ +kembalikanBuku()         │
│ +getBukuPopuler()         │
└───────────────────────────┘
```

Notasi UML pada diagram di atas:

-   `<>·····>` : Asosiasi (association) - menunjukkan controller beroperasi pada model
-   `───────>` : Agregasi (aggregation) - menunjukkan relasi "memiliki"
-   `◄───────` : Komposisi (composition) - menunjukkan relasi "bagian dari" yang lebih kuat
-   `1` dan `*` : Kardinalitas - menunjukkan jumlah relasi (1-to-many, many-to-many, dll)

````

### Penjelasan Relasi pada Class Diagram

1. **Controller ke Model (MVC Architecture)**

    - **AdminController ke User/AdminModel**

        - Deskripsi: AdminController mengoperasikan User dan AdminModel untuk mengelola data admin
        - Metode penting: `showAdminData()`, `showProfile()`, `editProfile()`, `updateProfile()`
        - Operasi: Membaca dan memperbarui data profil admin

    - **BukuController ke BukuModel/KategoriModel**

        - Deskripsi: BukuController mengoperasikan BukuModel untuk manajemen data buku
        - Metode penting: `index()`, `tambah()`, `simpan()`, `detail()`, `edit()`, `update()`, `hapus()`
        - Operasi: CRUD untuk buku dan relasi kategorinya, termasuk generasi QR code dan peminjaman

    - **KategoriController ke KategoriModel**

        - Deskripsi: KategoriController mengoperasikan KategoriModel untuk mengelola kategori buku
        - Metode penting: `index()`, `detail()`, `tambah()`, `simpan()`, `edit()`, `update()`, `hapus()`
        - Operasi: CRUD untuk kategori buku dan menampilkan buku yang terkait dengan kategori

    - **PeminjamanController ke PeminjamanModel/BukuModel**
        - Deskripsi: PeminjamanController mengoperasikan PeminjamanModel untuk mengelola transaksi peminjaman
        - Metode penting: `index()`, `formPinjam()`, `pinjamBuku()`, `kembalikanBuku()`, `updateLateStatus()`
        - Operasi: Mengelola seluruh siklus hidup peminjaman, termasuk pendeteksian keterlambatan

2. **User ke Profile Models (AdminModel, SiswaModel, GuruModel, StaffModel)**

    - Relasi: One-to-One (1:1)
    - Implementasi: `User` memiliki metode-metode seperti `admin()`, `siswa()`, `guru()`, dan `staff()`
    - Deskripsi: Setiap User memiliki tepat satu profil (admin, siswa, guru, atau staff) berdasarkan nilai `level`

3. **User ke PeminjamanModel**

    - Relasi: One-to-Many (1:N)
    - Implementasi: `PeminjamanModel` memiliki metode `user()` yang mengembalikan `belongsTo`
    - Deskripsi: Satu User dapat melakukan banyak peminjaman buku

4. **BukuModel ke PeminjamanModel**

    - Relasi: One-to-Many (1:N)
    - Implementasi: `PeminjamanModel` memiliki metode `buku()` yang mengembalikan `belongsTo`
    - Deskripsi: Satu Buku dapat dipinjam berkali-kali (dalam transaksi berbeda)

5. **BukuModel ke KategoriModel**
    - Relasi: Many-to-Many (N:M)
    - Implementasi: Menggunakan tabel penghubung `kategori_buku` dengan metode `belongsToMany`
    - Deskripsi: Satu Buku dapat memiliki banyak Kategori, dan satu Kategori dapat memiliki banyak Buku

### Alur Operasi Controller pada Model

1. **Siklus Hidup Manajemen Buku**

    ```
    BukuController.tambah() → BukuController.simpan() → BukuModel.save() → BukuModel.kategori().attach()
    ```

    - Membuat instance BukuModel baru
    - Mengisi properti model dari request input
    - Menyimpan model ke database
    - Melampirkan kategori yang dipilih melalui relasi many-to-many

2. **Siklus Hidup Peminjaman Buku**

    ```
    PeminjamanController.formPinjam() → PeminjamanController.pinjamBuku() →
    PeminjamanModel.save() → BukuModel.update() → BukuController.pinjamBuku()
    ```

    - Membuat instance PeminjamanModel baru
    - Mengisi data peminjaman dari request
    - Menyimpan transaksi peminjaman
    - Memperbarui stok buku yang dipinjam
    - Memperbarui status buku (Tersedia/Habis)

3. **Pengelolaan Status Keterlambatan**

    ```
    PeminjamanController.updateLateStatus() → PeminjamanModel.update() →
    PeminjamanController.index() → View
    ```

    - Secara otomatis memperbarui status peminjaman yang terlambat
    - Memeriksa tanggal kembali terhadap tanggal saat ini
    - Mengubah status peminjaman menjadi 'Terlambat' jika melewati batas
    - Menampilkan data peminjaman dengan status yang diperbarui

### Penggunaan Modifier Akses

-   **Public**: Digunakan untuk metode yang perlu diakses dari luar kelas, seperti metode relasi pada model dan metode aksi pada controller
-   **Protected**: Digunakan untuk properti yang hanya boleh diakses oleh kelas itu sendiri dan turunannya, seperti `$fillable` dan `$table`
-   **Private**: Digunakan untuk metode-metode helper pada controller yang hanya digunakan secara internal

### Manfaat Implementasi OOP dalam Sistem

1. **Modularitas**: Setiap kelas memiliki tanggung jawab spesifik yang terdefinisi dengan jelas
2. **Maintainability**: Kode lebih mudah dipelihara karena fokus pada satu fungsionalitas per kelas
3. **Reusability**: Logika umum diletakkan di kelas dasar yang bisa digunakan kembali
4. **Extensibility**: Sistem mudah diperluas dengan menambahkan kelas-kelas baru tanpa mengubah kode yang sudah ada
5. **Security**: Enkapsulasi membantu melindungi data sensitif dan mencegah akses tidak sah

## 📝 Lisensi

Sistem Informasi Perpustakaan MTSN 6 Garut dilisensikan di bawah [Lisensi MIT](LICENSE). Anda bebas menggunakan, memodifikasi, dan mendistribusikan kode dengan atribusi yang sesuai.
````
