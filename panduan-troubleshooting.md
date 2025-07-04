# Panduan Troubleshooting Sistem Notifikasi Perpustakaan

Dokumen ini berisi langkah-langkah untuk mendiagnosis dan memperbaiki masalah pada sistem notifikasi perpustakaan.

## Masalah Umum dan Solusinya

### 1. Service Tidak Berjalan

#### Gejala

-   Email notifikasi tidak terkirim
-   Perintah `nssm status LaravelQueueWorker` atau `nssm status LaravelScheduler` tidak menampilkan `SERVICE_RUNNING`

#### Solusi

1. Restart service:

    ```
    nssm restart LaravelQueueWorker
    nssm restart LaravelScheduler
    ```

2. Periksa log error:

    ```
    type D:\web\TA\perpustakaan\storage\logs\queue-worker-error.log
    type D:\web\TA\perpustakaan\storage\logs\scheduler-error.log
    ```

3. Pastikan path di file batch benar:

### 2. Email Tidak Terkirim

#### Gejala

-   Service berjalan, tapi email tidak sampai
-   Tidak ada error di log

#### Solusi

1. Periksa konfigurasi SMTP di file `.env`:

    ```
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=your-email@gmail.com
    MAIL_PASSWORD=app-password
    MAIL_ENCRYPTION=tls
    ```

2. Cek log Laravel:

    ```
    type D:\web\TA\perpustakaan\storage\logs\laravel.log
    ```

3. Pastikan email provider tidak memblokir:

    - Jika menggunakan Gmail, pastikan "Less secure app access" diaktifkan atau menggunakan App Password
    - Periksa apakah ada batasan pengiriman email per hari

4. Kirim email test manual:
    ```
    cd /d D:\web\TA\perpustakaan
    php artisan app:test-notification
    ```

### 3. Queue Menumpuk

#### Gejala

-   Email terkirim terlambat
-   Terlalu banyak email terkirim sekaligus

#### Solusi

1. Periksa tabel jobs di database:

    ```
    cd /d D:\web\TA\perpustakaan
    php artisan tinker
    DB::table('jobs')->count();
    ```

2. Jalankan worker dengan koneksi terpisah:

    ```
    cd /d D:\web\TA\perpustakaan
    php artisan queue:work database --queue=high,default --stop-when-empty
    ```

3. Reset queue jika terlalu menumpuk:
    ```
    php artisan queue:flush
    ```
    (Hati-hati: perintah ini akan menghapus semua job dalam queue)

### 4. Service Berhenti Otomatis

#### Gejala

-   Service berjalan beberapa saat, kemudian berhenti sendiri
-   Status service berubah menjadi `SERVICE_STOPPED`

#### Solusi

1. Periksa settings recovery di NSSM:

    ```
    nssm edit LaravelQueueWorker
    ```

    - Buka tab Dependencies dan pastikan tidak ada dependency yang salah
    - Buka tab Recovery dan set actions untuk restart service

2. Periksa Windows Event Log untuk melihat alasan service berhenti:

    - Buka Event Viewer (eventvwr.msc)
    - Periksa Windows Logs > Application untuk error terkait service

3. Atur timeout yang lebih tinggi di file queue-worker.bat:
    ```
    php artisan queue:work database --sleep=3 --tries=3 --timeout=120
    ```

### 5. Notifikasi Terlambat

#### Gejala

-   Email notifikasi tidak terkirim tepat waktu
-   Notifikasi terkirim beberapa jam setelah jadwal seharusnya

#### Solusi

1. Periksa jadwal di Kernel.php:

    ```php
    $schedule->command('app:send-pengembalian-reminders')
        ->hourly(3) // Pastikan frekuensi sesuai kebutuhan
    ```

2. Pastikan scheduler berjalan setiap menit:

    ```
    type D:\web\TA\perpustakaan\storage\logs\scheduler.log
    ```

    - Log harus menunjukkan entri setiap menit

3. Periksa zona waktu di file config/app.php:

    ```php
    'timezone' => 'Asia/Jakarta',
    ```

4. Restart service scheduler:
    ```
    nssm restart LaravelScheduler
    ```

## Langkah-langkah Pemeriksaan Mendalam

### Memeriksa Database

1. Jalankan tinker untuk memeriksa tabel queue:

    ```
    cd /d D:\web\TA\perpustakaan
    php artisan tinker
    DB::table('jobs')->count(); // Jumlah job dalam antrian
    DB::table('failed_jobs')->count(); // Jumlah job yang gagal
    ```

2. Periksa tabel peminjaman:
    ```
    use App\Models\PeminjamanModel;
    PeminjamanModel::where('tanggal_kembali', now()->format('Y-m-d'))->count();
    ```

### Memeriksa Log Secara Mendetail

1. Filter log untuk melihat error terkait email:

    ```
    cd /d D:\web\TA\perpustakaan
    findstr /i "error exception mail smtp" storage\logs\laravel.log
    ```

2. Lihat log queue worker:
    ```
    type D:\web\TA\perpustakaan\storage\logs\queue-worker-output.log
    ```

### Reset Sistem Notifikasi

Jika semua solusi di atas tidak berhasil, coba reset sistem notifikasi:

1. Hentikan service:

    ```
    nssm stop LaravelQueueWorker
    nssm stop LaravelScheduler
    ```

2. Bersihkan queue:

    ```
    cd /d D:\web\TA\perpustakaan
    php artisan queue:flush
    ```

3. Jalankan migrasi ulang untuk tabel jobs (jika perlu):

    ```
    php artisan migrate:refresh --path=database/migrations/0001_01_01_000002_create_jobs_table.php
    ```

4. Mulai ulang service:

    ```
    nssm start LaravelQueueWorker
    nssm start LaravelScheduler
    ```

5. Kirim notifikasi test:
    ```
    php artisan app:test-notification
    ```

## Kesimpulan

Dengan mengikuti langkah-langkah troubleshooting di atas, sebagian besar masalah pada sistem notifikasi perpustakaan dapat didiagnosis dan diperbaiki. Jika masalah masih berlanjut, periksa sumber daya sistem (CPU, memori, ruang disk) dan pertimbangkan untuk mengatur ulang service dengan konfigurasi yang lebih optimal.
