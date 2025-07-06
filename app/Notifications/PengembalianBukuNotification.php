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

        // Hitung hari terlambat
        $today = \Carbon\Carbon::today();
        $tanggalKembali = \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali);
        $hariTerlambat = $tanggalKembali->diffInDays($today, false);

        // Tentukan jenis notifikasi
        $isTerlambat = $hariTerlambat > 0;

        // Setting warna tombol berdasarkan status
        $mail->level = $isTerlambat ? 'error' : 'success';

        // Subject email berdasarkan status
        if ($isTerlambat) {
            $mail->subject("⚠️ TERLAMBAT: Pengembalian Buku ({$hariTerlambat} hari)");
        } else {
            $mail->subject('🔔 Pengingat: Batas Waktu Pengembalian Buku Hari Ini');
        }

        // Greeting
        $mail->greeting('Halo ' . $notifiable->nama . ',');

        // Menambahkan logo langsung dalam email sebagai HTML string
        $logoUrl = url('https://belajar.mtsn6pasuruan.com/__statics/img/logo.png');
        $logoHtml = '<div style="text-align: center; margin-bottom: 20px;">
            <img src="' . $logoUrl . '"
                alt="Logo Perpustakaan"
                style="max-width: 150px; max-height: 150px;">
        </div>';
        $mail->line(new HtmlString($logoHtml));

        // Pesan utama berdasarkan status
        if ($isTerlambat) {
            $mail->line(new HtmlString('<div style="background-color: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin: 10px 0;">'));
            $mail->line(new HtmlString('<strong style="color: #d00;">⚠️ PERHATIAN: Buku Anda sudah terlambat ' . $hariTerlambat . ' hari!</strong>'));
            $mail->line(new HtmlString('</div>'));
            $mail->line('Mohon segera kembalikan buku berikut untuk menghindari sanksi lebih lanjut:');
        } else {
            $mail->line(new HtmlString('<div style="background-color: #eff; border: 1px solid #cdf; padding: 15px; border-radius: 5px; margin: 10px 0;">'));
            $mail->line(new HtmlString('<strong style="color: #06c;">🔔 Pengingat: Buku harus dikembalikan hari ini!</strong>'));
            $mail->line(new HtmlString('</div>'));
            $mail->line('Kami mengingatkan bahwa Anda memiliki buku yang harus dikembalikan hari ini:');
        }

        // Informasi peminjaman buku
        $mail->line(new HtmlString('<div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0;">'));
        $mail->line(new HtmlString('<strong>📚 Judul Buku:</strong> ' . $this->peminjaman->buku->judul));
        $mail->line(new HtmlString('<strong>🔢 No. Peminjaman:</strong> ' . $this->peminjaman->no_peminjaman));
        $mail->line(new HtmlString('<strong>📅 Tanggal Peminjaman:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d F Y')));
        $mail->line(new HtmlString('<strong>⏰ Batas Waktu Pengembalian:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_kembali)->translatedFormat('d F Y')));

        if ($isTerlambat) {
            $mail->line(new HtmlString('<strong style="color: #d00;">⚠️ Status:</strong> <span style="color: #d00;">Terlambat ' . $hariTerlambat . ' hari</span>'));
        }
        $mail->line(new HtmlString('</div>'));

        // Pesan penutup berdasarkan status
        if ($isTerlambat) {
            $mail->line('Segera kembalikan buku untuk menghindari sanksi tambahan. Keterlambatan dapat dikenakan denda sesuai peraturan perpustakaan.');
        } else {
            $mail->line('Mohon segera kembalikan buku tersebut sebagai bentuk tanggung jawab terhadap fasilitas perpustakaan bersama.');
        }

        // Tambahkan tombol action
        $mail->action('Lihat Detail Peminjaman', url('/peminjaman'));

        // Tambahkan penutup
        $mail->line('Terima kasih atas perhatian dan kerjasamanya.');
        $mail->salutation("Petugas,  \nPerpustakaan MTSN 6 Garut");

        return $mail;
    }
}
