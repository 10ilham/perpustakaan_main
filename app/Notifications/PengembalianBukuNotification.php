<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PeminjamanModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

\Carbon\Carbon::setLocale('id'); //agar format tanggal bulan tahun menggunakan indonesia

/**
 * Kelas notifikasi untuk mengirimkan pengingat pengembalian buku
 * Implements ShouldQueue agar notifikasi dikirim secara asinkron (berjalan di latar belakang)
 */
class PengembalianBukuNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $peminjaman;

    /**
     * Konstruktor untuk membuat instance notifikasi baru.
     *
     * @param PeminjamanModel $peminjaman - Data peminjaman buku yang akan dinotifikasikan
     */
    public function __construct(PeminjamanModel $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    /**
     * Menentukan channel yang digunakan untuk mengirim notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Membangun representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Menggunakan StandardMailMessage standar Laravel yang lebih sederhana
        $mail = new MailMessage;

        // Setting warna tombol ke hijau
        $mail->level = 'success';

        $mail->subject('Pengingat: Batas Waktu Pengembalian Buku Hari Ini');

        // Menambahkan logo langsung dalam email sebagai HTML string
        $mail->greeting('Halo ' . $notifiable->nama . ',');

        // Menggunakan URL logo
        $logoUrl = url('https://belajar.mtsn6pasuruan.com/__statics/img/logo.png');

        // Membuat HTML untuk menampilkan logo
        $logoHtml = '<div style="text-align: center; margin-bottom: 20px;">
            <img src="' . $logoUrl . '"
                alt="Logo Perpustakaan"
                style="max-width: 150px; max-height: 150px;">
        </div>';

        $mail->line(new HtmlString($logoHtml));

        // Tambahkan informasi peminjaman buku
        $mail->line('Kami ingin mengingatkan bahwa Anda memiliki buku yang harus dikembalikan hari ini:');
        $mail->line(new HtmlString('<strong>Judul Buku:</strong> ' . $this->peminjaman->buku->judul));
        $mail->line(new HtmlString('<strong>No. Peminjaman:</strong> ' . $this->peminjaman->no_peminjaman));
        $mail->line(new HtmlString('<strong>Tanggal Peminjaman:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d F Y'))); //translatedFormat untuk ubah format eng to indonesia
        $mail->line(new HtmlString('<strong>Batas Waktu Pengembalian:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali)->translatedFormat('d F Y')));
        $mail->line('Mohon segera kembalikan buku tersebut sebagai bentuk tanggung jawab terhadap fasilitas perpustakaan bersama.');

        // Tambahkan tombol action
        $mail->action('Lihat Detail Peminjaman', url('/peminjaman'));

        // Tambahkan penutup
        $mail->line('Terima kasih atas perhatian dan kerjasamanya.');
        $mail->salutation("Petugas,  \nPerpustakaan MTSN 6 Garut");

        return $mail;
    }
}
