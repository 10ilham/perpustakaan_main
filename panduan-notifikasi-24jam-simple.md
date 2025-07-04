## Arsitektur Sistem

Sistem notifikasi perpustakaan MTSN 6 Garut menggunakan arsitektur berikut:

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

## Komponen Sistem

### 1. Laravel Scheduler (LaravelScheduler)

-   **Fungsi**: Menjalankan command `app:send-pengembalian-reminders` setiap 1 jam
-   **Implementasi**: Windows Service menggunakan NSSM
-   **File Konfigurasi**: `scheduler.bat`, yang menjalankan `php artisan schedule:run` setiap menit
-   **Jadwal**: Diatur di `routes\console.php`

### 2. Database Queue

-   **Fungsi**: Menyimpan job notifikasi email dalam database agar tidak hilang jika server restart
-   **Tabel Database**: `jobs`, `failed_jobs`, dan `job_batches`
-   **Konfigurasi**: Diatur di `config/queue.php` dengan driver `database`

### 3. Laravel Queue Worker (LaravelQueueWorker)

-   **Fungsi**: Memproses job dalam antrian untuk mengirim email
-   **Implementasi**: Windows Service menggunakan NSSM
-   **File Konfigurasi**: `queue-worker.bat`, yang menjalankan `php artisan queue:work`
-   **Parameter**: `--sleep=3 --tries=3 --timeout=60 --max-time=0`

### 4. SMTP Server

-   **Fungsi**: Mengirim email notifikasi ke peminjam
-   **Konfigurasi**: Diatur di file `.env` dengan parameter MAIL\_\*

## Alur Kerja

1. **Scheduler** berjalan setiap menit untuk memeriksa jadwal
2. Setiap 1 jam, **Scheduler** menjalankan perintah `app:send-pengembalian-reminders`
3. Command `SendPengembalianReminders` mencari peminjaman yang perlu dinotifikasi
4. Untuk setiap peminjaman, notifikasi `PengembalianBukuNotification` dibuat
5. Notifikasi tersebut dimasukkan ke **Database Queue** untuk diproses asinkron
6. **Queue Worker** mengambil notifikasi dari antrian dan memprosesnya
7. Notifikasi dikirim melalui **SMTP Server** ke email peminjam

# Panduan Menjalankan Notifikasi 24 Jam

Dokumen ini berisi panduan singkat untuk menjalankan sistem notifikasi perpustakaan 24 jam.

-   Akun Windows dengan hak administrator

## Konfigurasi yang Sudah Dilakukan

1. **Pengaturan jadwal di Kernel.php**:

    - Notifikasi pengembalian dijadwalkan berjalan setiap hari pukul 07:00 pagi
    - Notifikasi pengembalian juga dijadwalkan berjalan setiap jam
    - Test notifikasi dijadwalkan berjalan setiap menit (untuk keperluan testing)

2. **Script batch yang sudah disiapkan**:
    - `scheduler.bat` - Untuk menjalankan Laravel scheduler setiap menit
    - `queue-worker.bat` - Untuk memproses antrian notifikasi email

## Langkah Menjalankan Sistem Notifikasi 24 Jam

### 1. Menggunakan NSSM untuk Mengatur Service Windows

#### Mengunduh dan Menyiapkan NSSM

1. Unduh NSSM dari [situs resminya](https://nssm.cc/download)
2. Ekstrak file zip ke folder tertentu (misalnya `C:\nssm`)
3. Tambahkan path NSSM ke environment variable Path:
    - Klik kanan My Computer > Properties > Advanced System Settings
    - Klik Environment Variables
    - Di bagian System Variables, cari Path dan klik Edit
    - Tambahkan `C:\nssm\win64` (sesuaikan dengan lokasi folder NSSM)
    - Klik OK untuk menyimpan

#### Membuat Service untuk Queue Worker

1. Buka Command Prompt sebagai Administrator
2. Jalankan perintah:
    ```
    nssm install LaravelQueueWorker
    ```
3. Pada form yang muncul, isi:
    - **Path**: `C:\Windows\System32\cmd.exe`
    - **Startup directory**: `D:\web\TA\perpustakaan`
    - **Arguments**: `/c D:\web\TA\perpustakaan\queue-worker.bat`
4. Di tab Details:
    - **Display name**: LaravelQueueWorker
    - **Description**: Laravel Queue Worker Service untuk Perpustakaan
    - **Startup type**: Automatic
5. Di tab I/O:
    - **Output (stdout)**: `D:\web\TA\perpustakaan\storage\logs\queue-worker-output.log`
    - **Error (stderr)**: `D:\web\TA\perpustakaan\storage\logs\queue-worker-error.log`
6. Klik "Install service"

#### Membuat Service untuk Scheduler

1. Buka Command Prompt sebagai Administrator
2. Jalankan perintah:
    ```
    nssm install LaravelScheduler
    ```
3. Pada form yang muncul, isi:
    - **Path**: `C:\Windows\System32\cmd.exe`
    - **Startup directory**: `D:\web\TA\perpustakaan`
    - **Arguments**: `/c D:\web\TA\perpustakaan\scheduler.bat`
4. Klik "Install service"

### 2. Memulai Service

1. Buka Command Prompt sebagai Administrator
2. Jalankan perintah:
    ```
    nssm start LaravelQueueWorker
    nssm start LaravelScheduler
    ```

### 3. Verifikasi Sistem Berjalan

1. Cek status service:
    ```
    nssm status LaravelQueueWorker
    nssm status LaravelScheduler
    ```
    Kedua perintah seharusnya menampilkan `SERVICE_RUNNING`
2. Periksa log notifikasi:
    ```
    type D:\web\TA\perpustakaan\storage\logs\pengembalian-reminders.log
    type D:\web\TA\perpustakaan\storage\logs\pengembalian-reminders-hourly.log
    type D:\web\TA\perpustakaan\storage\logs\test-notification.log
    ```
3. Perintah untuk melihat nssm yang terinstall :
    ```
    reg query HKLM\SYSTEM\CurrentControlSet\Services /s /f "nssm.exe"
    ```

## Mengatur Service Untuk Mulai Otomatis Saat Windows Restart

1. Buka Command Prompt sebagai Administrator
2. Jalankan perintah berikut:
    ```
    sc config LaravelQueueWorker start= auto
    sc config LaravelScheduler start= auto
    ```
    (Perhatikan spasi setelah tanda =)

## Troubleshooting

### Service Tidak Berjalan

1. Periksa status service:

    ```
    nssm status LaravelQueueWorker
    nssm status LaravelScheduler
    ```

2. Jika tidak berjalan, restart:

    ```
    nssm restart LaravelQueueWorker
    nssm restart LaravelScheduler
    ```

3. Periksa log error:
    ```
    type D:\web\TA\perpustakaan\storage\logs\laravel.log
    ```

### Email Tidak Terkirim

1. Periksa konfigurasi email di file `.env`
   Pastikan konfigurasi email di file `.env` sudah benar:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Jika menggunakan Gmail, Anda perlu menggunakan App Password, bukan password biasa. 2. Coba kirim notifikasi test:
`php artisan app:test-notification` 3. Periksa queue:
`php artisan queue:status`

### Menghapus Service

Jika perlu menghapus service:

```
nssm remove LaravelQueueWorker
nssm remove LaravelScheduler
```

### Mengedit Service

Untuk mengedit konfigurasi service:

```
nssm edit LaravelQueueWorker
nssm edit LaravelScheduler
```

### Memulai / Menghentikan / Me-restart Service

```
nssm start LaravelQueueWorker
nssm stop LaravelQueueWorker
nssm restart LaravelQueueWorker

nssm start LaravelScheduler
nssm stop LaravelScheduler
nssm restart LaravelScheduler
```

## Pemeliharaan Berkala

-   **Mingguan**: Periksa file log, bersihkan jika terlalu besar
-   **Bulanan**: Restart service untuk mencegah memory leak
-   **Setelah Update**: Restart service setelah mengupdate kode aplikasi

## Monitoring

1. Periksa log aplikasi secara berkala:

    - `storage/logs/laravel.log`
    - `storage/logs/queue-worker-output.log`
    - `storage/logs/scheduler-output.log`

2. Periksa status service:
    ```
    nssm status LaravelQueueWorker
    nssm status LaravelScheduler
    ```

Dokumentasi Troubleshooting:

1. [**Panduan Troubleshooting**](./panduan-troubleshooting.md) - Mendiagnosis dan memperbaiki masalah

## Kesimpulan

Dengan mengikuti langkah-langkah di atas, sistem notifikasi perpustakaan akan berjalan 24 jam non-stop bahkan saat laptop dimatikan, karena telah diatur sebagai Windows Service yang berjalan secara otomatis.
