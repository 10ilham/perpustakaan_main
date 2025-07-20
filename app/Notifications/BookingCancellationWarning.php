<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PeminjamanModel;
use Illuminate\Support\HtmlString;

\Carbon\Carbon::setLocale('id');

/**
 * Kelas notifikasi untuk mengirimkan peringatan pembatalan booking ke user
 * Implements ShouldQueue agar notifikasi dikirim secara asinkron (berjalan di latar belakang)
 */
class BookingCancellationWarning extends Notification implements ShouldQueue
{
    use Queueable;

    protected $peminjaman;
    protected $cancelledCount;
    protected $isBlacklisted;

    /**
     * Konstruktor untuk membuat instance notifikasi baru.
     *
     * @param PeminjamanModel $peminjaman - Data peminjaman yang dibatalkan
     * @param int $cancelledCount - Jumlah pembatalan booking
     * @param bool $isBlacklisted - Status blacklist user
     */
    public function __construct(PeminjamanModel $peminjaman, int $cancelledCount, bool $isBlacklisted)
    {
        $this->peminjaman = $peminjaman;
        $this->cancelledCount = $cancelledCount;
        $this->isBlacklisted = $isBlacklisted;
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
        $mail = new MailMessage;

        // Setting warna tombol ke merah untuk peringatan
        $mail->level = 'error';

        $mail->subject('⚠️ Peringatan: Booking Buku Dibatalkan Otomatis');

        // Menambahkan logo langsung dalam email sebagai HTML string
        $mail->greeting('Halo ' . $this->peminjaman->user->nama . ',');

        // Menggunakan URL logo
        $logoUrl = url('https://belajar.mtsn6pasuruan.com/__statics/img/logo.png');

        // Membuat HTML untuk menampilkan logo
        $logoHtml = '<div style="text-align: center; margin-bottom: 20px;">
            <img src="' . $logoUrl . '"
                alt="Logo Perpustakaan"
                style="max-width: 150px; max-height: 150px;">
        </div>';

        $mail->line(new HtmlString($logoHtml));

        // Informasi booking yang dibatalkan
        $mail->line('Booking buku Anda telah dibatalkan secara otomatis karena tidak diambil dalam batas waktu jam sekolah.');

        $mail->line(new HtmlString('<strong>Detail Booking yang Dibatalkan:</strong>'));
        $mail->line(new HtmlString('<strong>No. Peminjaman:</strong> ' . $this->peminjaman->no_peminjaman));
        $mail->line(new HtmlString('<strong>Judul Buku:</strong> ' . $this->peminjaman->buku->judul));
        $mail->line(new HtmlString('<strong>Kode Buku:</strong> ' . $this->peminjaman->buku->kode_buku));
        $mail->line(new HtmlString('<strong>Tanggal Booking:</strong> ' . \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d F Y H:i')));

        $mail->line('');
        $mail->line(new HtmlString('<strong>📋 Aturan Pengambilan Buku:</strong>'));
        $mail->line('• Buku yang di-booking harus diambil pada hari yang sama');
        $mail->line('• Pengambilan hanya dapat dilakukan pada jam sekolah (sampai pukul 16:00)');
        $mail->line('• Booking akan dibatalkan otomatis jika tidak diambil tepat waktu');

        $mail->line('');
        $mail->line(new HtmlString('<strong>⚠️ Status Peringatan Anda:</strong>'));
        $mail->line(new HtmlString('Jumlah pembatalan booking: <strong>' . $this->cancelledCount . ' kali</strong>'));

        if ($this->isBlacklisted) {
            $mail->line(new HtmlString('<div style="background-color: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 5px; margin: 10px 0;">
                <strong style="color: #c33;">🚫 AKUN ANDA TELAH DI-BLACKLIST!</strong><br>
                Karena Anda telah membatalkan booking sebanyak 3 kali atau lebih, akun Anda tidak dapat melakukan peminjaman selama <strong>7 hari</strong>.<br>
                Blacklist akan berakhir pada tanggal: <strong>' . \Carbon\Carbon::now()->addDays(7)->translatedFormat('d F Y') . '</strong>
            </div>'));
        } else {
            $remainingChances = 3 - $this->cancelledCount;
            if ($remainingChances > 0) {
                $mail->line(new HtmlString('<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 10px 0;">
                    <strong style="color: #856404;">⚠️ PERINGATAN:</strong><br>
                    Anda memiliki <strong>' . $remainingChances . ' kesempatan lagi</strong> sebelum akun di-blacklist.<br>
                    Jika membatalkan booking lagi, Anda akan dilarang meminjam buku selama 1 minggu.
                </div>'));
            }
        }

        $mail->line('');
        $mail->line(new HtmlString('<strong>💡 Tips untuk menghindari pembatalan:</strong>'));
        $mail->line('• Pastikan Anda dapat mengambil buku pada hari yang sama');
        $mail->line('• Koordinasikan waktu pengambilan dengan jadwal sekolah');
        $mail->line('• Jika berhalangan, batalkan booking secara manual sebelum batas waktu');

        // Tambahkan tombol action
        if (!$this->isBlacklisted) {
            $mail->action('Lihat Riwayat Peminjaman', url('/anggota/peminjaman'));
        }

        // Tambahkan penutup
        $mail->line('Mohon patuhi aturan perpustakaan agar layanan dapat berjalan dengan baik untuk semua anggota.');
        $mail->salutation('Sistem Informasi Perpustakaan MTSN 6 Garut');

        return $mail;
    }
}
