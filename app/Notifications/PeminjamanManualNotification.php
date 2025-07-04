<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PeminjamanModel;
use Illuminate\Support\HtmlString;

\Carbon\Carbon::setLocale('id'); //agar format tanggal bulan tahun menggunakan indonesia

/**
 * Kelas notifikasi untuk mengirimkan pemberitahuan ke anggota saat admin melakukan peminjaman manual
 * Implements ShouldQueue agar notifikasi dikirim secara asinkron (berjalan di latar belakang)
 */
class PeminjamanManualNotification extends Notification implements ShouldQueue
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

        $mail->subject('Pemberitahuan: Peminjaman Buku Baru Untuk Anda');

        // Greeting
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
        $mail->line('Admin perpustakaan telah melakukan peminjaman buku untuk Anda:');
        $mail->line(new HtmlString('<strong>No. Peminjaman:</strong> ' . $this->peminjaman->no_peminjaman));
        $mail->line(new HtmlString('<strong>Judul Buku:</strong> ' . $this->peminjaman->buku->judul));
        $mail->line(new HtmlString('<strong>Kode Buku:</strong> ' . $this->peminjaman->buku->kode_buku));
        $mail->line(new HtmlString('<strong>Tanggal Peminjaman:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d F Y')));
        $mail->line(new HtmlString('<strong>Tanggal Kembali:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali)->translatedFormat('d F Y')));

        if (!empty($this->peminjaman->catatan)) {
            $mail->line(new HtmlString('<strong>Catatan:</strong> ' . $this->peminjaman->catatan));
        }

        $mail->line('Buku dapat diambil di perpustakaan dengan menunjukkan nomor peminjaman atau identitas Anda.');

        // Tambahkan tombol action untuk melihat detail peminjaman
        $mail->action('Lihat Detail Peminjaman', url('/peminjaman/detail/' . $this->peminjaman->id));

        // Tambahkan penutup
        $mail->line('Harap kembalikan buku tepat waktu untuk menghindari keterlambatan.');
        $mail->salutation('Sistem Informasi Perpustakaan MTSN 6 Garut');

        return $mail;
    }
}
