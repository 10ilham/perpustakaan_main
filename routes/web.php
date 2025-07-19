<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KirimEmailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\LevelMiddleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    // Jika pengguna sudah login, arahkan ke dashboard yang sesuai
    if (Auth::check()) {
        $userLevel = Auth::user()->level;

        if ($userLevel === 'admin') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/anggota/dashboard');
        }
    }

    return view('layouts.index');
});

Route::get('formemail', [KirimEmailController::class, 'index']);
Route::post('kirim', [KirimEmailController::class, 'kirim']);

// Route untuk login - hanya dapat diakses oleh tamu (belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Route untuk lupa password dan reset password
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// Route untuk verifikasi email - dapat diakses baik oleh tamu maupun pengguna yang sudah login
Route::get('/email/verify/{token}', [VerificationController::class, 'verify'])->name('email.verify');

// Route untuk logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('success', 'Anda telah berhasil logout.');
})->name('logout');

// Route untuk dashboard user, memanggil middleware LevelMiddleware
Route::middleware(['auth', LevelMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'showAdminData'])->name('admin.dashboard');
    Route::get('/admin/chart-data', [AdminController::class, 'getChartData'])->name('admin.chart-data');
    Route::get('/anggota/dashboard', [AnggotaController::class, 'showAnggotaData'])->name('anggota.dashboard');
    Route::get('/anggota/chart-data', [AnggotaController::class, 'getChartData'])->name('anggota.chart-data');

    // Route untuk pengelolaan anggota (khusus admin)
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/detail/{id}', [AnggotaController::class, 'detail'])->name('anggota.detail');
    Route::get('/anggota/tambah', [AnggotaController::class, 'tambah'])->name('anggota.tambah');
    Route::post('/anggota/simpan', [AnggotaController::class, 'simpan'])->name('anggota.simpan');
    Route::get('/anggota/edit/{id}', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::post('/anggota/update/{id}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::post('/anggota/hapus/{id}', [AnggotaController::class, 'hapus'])->name('anggota.hapus');

    // Route untuk buku
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/detail/{id}', [BukuController::class, 'detail'])->name('buku.detail');
    Route::get('/buku/tambah', [BukuController::class, 'tambah'])->name('buku.tambah');
    Route::post('/buku/simpan', [BukuController::class, 'simpan'])->name('buku.simpan');
    Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
    Route::post('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::post('/buku/hapus/{id}', [BukuController::class, 'hapus'])->name('buku.hapus');
    Route::get('/buku/{id}/qrcode-download', [BukuController::class, 'downloadQrCode'])->name('buku.qrcode.download');
    Route::get('/buku/export/all', [BukuController::class, 'getAllBooksForExport'])->name('buku.export.all');

    // Route untuk kategori
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/detail/{id}', [KategoriController::class, 'detail'])->name('kategori.detail');
    Route::get('/kategori/tambah', [KategoriController::class, 'tambah'])->name('kategori.tambah');
    Route::post('/kategori/simpan', [KategoriController::class, 'simpan'])->name('kategori.simpan');
    Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::post('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::post('/kategori/hapus/{id}', [KategoriController::class, 'hapus'])->name('kategori.hapus');

    // Route untuk peminjaman buku
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/form/{id}', [PeminjamanController::class, 'formPinjam'])->name('peminjaman.form');
    Route::post('/peminjaman/pinjam', [PeminjamanController::class, 'pinjamBuku'])->name('peminjaman.pinjam');
    Route::get('/peminjaman/detail/{id}', [PeminjamanController::class, 'detail'])->name('peminjaman.detail');
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikanBuku'])->name('peminjaman.kembalikan');
    Route::delete('/peminjaman/hapus/{id}', [PeminjamanController::class, 'hapusPeminjaman'])->name('peminjaman.hapus');
    Route::post('/peminjaman/{id}/konfirmasi-pengambilan', [PeminjamanController::class, 'konfirmasiPengambilan'])->name('peminjaman.konfirmasi-pengambilan');

    // Route untuk peminjaman manual (khusus admin)
    Route::get('/peminjaman/manual', [PeminjamanController::class, 'formManual'])->name('peminjaman.manual');
    Route::post('/peminjaman/manual/simpan', [PeminjamanController::class, 'simpanManual'])->name('peminjaman.manual.simpan');
    Route::get('/peminjaman/manual/get-anggota/{level}', [PeminjamanController::class, 'getAnggotaByLevel'])->name('peminjaman.manual.anggota');

    // Route untuk sanksi
    Route::post('/peminjaman/proses-pengembalian', [App\Http\Controllers\SanksiController::class, 'prosesPengembalian'])->name('sanksi.proses-pengembalian');
    Route::get('/sanksi', [App\Http\Controllers\SanksiController::class, 'index'])->name('sanksi.index');
    Route::post('/sanksi/{id}/bayar', [App\Http\Controllers\SanksiController::class, 'bayar'])->name('sanksi.bayar');

    // Route untuk laporan (hanya dapat diakses oleh admin)
    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/belum_kembali', [App\Http\Controllers\LaporanController::class, 'belumKembali'])->name('laporan.belum_kembali');
    Route::get('/laporan/sudah_kembali', [App\Http\Controllers\LaporanController::class, 'sudahKembali'])->name('laporan.sudah_kembali');
    Route::get('/laporan/buku_log', [App\Http\Controllers\BukuLogController::class, 'index'])->name('laporan.buku_log');
    Route::delete('/laporan/buku-log/{id}', [App\Http\Controllers\BukuLogController::class, 'destroy'])->name('laporan.buku_log.destroy');
    Route::get('/laporan/sanksi', [App\Http\Controllers\LaporanController::class, 'sanksi'])->name('laporan.sanksi');
    Route::get('/laporan/sanksi/belum-bayar', [App\Http\Controllers\LaporanController::class, 'sanksiBelumBayar'])->name('laporan.sanksi.belum_bayar');
    Route::get('/laporan/sanksi/sudah-bayar', [App\Http\Controllers\LaporanController::class, 'sanksiSudahBayar'])->name('laporan.sanksi.sudah_bayar');
    Route::get('/laporan/chart-data', [App\Http\Controllers\LaporanController::class, 'getChartData'])->name('laporan.chart-data');
    Route::get('/laporan/pie-chart-data', [App\Http\Controllers\LaporanController::class, 'getPieChartData'])->name('laporan.pie-chart-data');

    // Route untuk profile
    Route::get('/admin/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
    Route::get('/siswa/profile', [SiswaController::class, 'showProfile'])->name('siswa.profile');
    Route::get('/guru/profile', [GuruController::class, 'showProfile'])->name('guru.profile');
    Route::get('/staff/profile', [StaffController::class, 'showProfile'])->name('staff.profile');

    // Route untuk edit profile
    Route::get('/admin/profile/edit', [AdminController::class, 'editProfile'])->name('admin.profile.edit');
    Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('/siswa/profile/edit', [SiswaController::class, 'editProfile'])->name('siswa.profile.edit');
    Route::post('/siswa/profile/update', [SiswaController::class, 'updateProfile'])->name('siswa.profile.update');
    Route::get('/guru/profile/edit', [GuruController::class, 'editProfile'])->name('guru.profile.edit');
    Route::post('/guru/profile/update', [GuruController::class, 'updateProfile'])->name('guru.profile.update');
    Route::get('/staff/profile/edit', [StaffController::class, 'editProfile'])->name('staff.profile.edit');
    Route::post('/staff/profile/update', [StaffController::class, 'updateProfile'])->name('staff.profile.update');

    // Fallback GET routes untuk profile update (untuk handling direct URL access)
    Route::get('/admin/profile/update', function () {
        return redirect('/admin/profile/edit')->with('success', 'Silakan gunakan form edit untuk memperbarui profil.');
    });
    Route::get('/siswa/profile/update', function () {
        return redirect('/siswa/profile/edit')->with('success', 'Silakan gunakan form edit untuk memperbarui profil.');
    });
    Route::get('/guru/profile/update', function () {
        return redirect('/guru/profile/edit')->with('success', 'Silakan gunakan form edit untuk memperbarui profil.');
    });
    Route::get('/staff/profile/update', function () {
        return redirect('/staff/profile/edit')->with('success', 'Silakan gunakan form edit untuk memperbarui profil.');
    });
});
