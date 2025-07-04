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
 * Kelas notifikasi untuk mengirimkan pemberitahuan ke admin saat ada peminjaman buku baru
 * Implements ShouldQueue agar notifikasi dikirim secara asinkron (berjalan di latar belakang)
 */
class PeminjamanBukuAdminNotification extends Notification implements ShouldQueue
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

        // Setting warna tombol ke biru
        $mail->level = 'info';

        $mail->subject('Pemberitahuan: Peminjaman Buku Baru');

        // Menambahkan logo langsung dalam email sebagai HTML string
        $mail->greeting('Halo Petugas Perpustakaan,');

        // Menggunakan URL logo
        $logoUrl = url('https://belajar.mtsn6pasuruan.com/__statics/img/logo.png');

        // Membuat HTML untuk menampilkan logo
        $logoHtml = '<div style="text-align: center; margin-bottom: 20px;">
            <img src="' . $logoUrl . '"
                alt="Logo Perpustakaan"
                style="max-width: 150px; max-height: 150px;">
        </div>';

        $mail->line(new HtmlString($logoHtml));

        // Informasi user yang meminjam
        $userInfo = $this->peminjaman->user;
        $userName = $userInfo->nama ?? 'N/A';
        $userLevel = $userInfo->level ?? 'N/A';

        // Tambahkan informasi peminjaman buku
        $mail->line('Ada permintaan peminjaman buku baru yang perlu Anda siapkan:');
        $mail->line(new HtmlString('<strong>No. Peminjaman:</strong> ' . $this->peminjaman->no_peminjaman));
        $mail->line(new HtmlString('<strong>Judul Buku:</strong> ' . $this->peminjaman->buku->judul));
        $mail->line(new HtmlString('<strong>Kode Buku:</strong> ' . $this->peminjaman->buku->kode_buku));
        $mail->line(new HtmlString('<strong>Peminjam:</strong> ' . $userName . ' (' . ucfirst($userLevel) . ')'));
        $mail->line(new HtmlString('<strong>Tanggal Peminjaman:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d F Y'))); //translatedFormat untuk ubah format eng to indonesia
        $mail->line(new HtmlString('<strong>Tanggal Kembali:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali)->translatedFormat('d F Y')));

        $mail->line('Mohon segera siapkan buku tersebut agar peminjam tidak perlu menunggu lama saat pengambilan.');

        // Tambahkan tombol action
        $mail->action('Lihat Detail Peminjaman', url('/admin/peminjaman'));

        // Tambahkan penutup
        $mail->line('Terima kasih atas kerjasamanya.');
        $mail->salutation('Sistem Informasi Perpustakaan MTSN 6 Garut');

        return $mail;
    }
}
