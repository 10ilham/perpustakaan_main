<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\AdminModel;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BukuController extends Controller
{
    /**
     * Tampilkan daftar buku dengan filter dan pencarian - ELOQUENT
     */
    public function index(Request $request)
    {
        // Query dasar dengan relasi kategori - ELOQUENT
        $query = BukuModel::with('kategori');

        // Filter berdasarkan kategori - ELOQUENT
        if ($request->filled('kategori')) {
            $query->whereHas('kategori', fn($q) => $q->where('kategori.id', $request->kategori));
        }

        // Filter berdasarkan status - ELOQUENT
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian judul - ELOQUENT
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Hitung statistik menggunakan clone query - ELOQUENT
        $totalQuery = clone $query;
        $totalBuku = $totalQuery->count();
        $tersedia = (clone $totalQuery)->where('status', 'Tersedia')->count();
        $habis = (clone $totalQuery)->where('status', 'Habis')->count();

        // Ambil data dengan pagination - ELOQUENT
        $buku = $query->paginate(8)->appends($request->all());

        // Ambil semua kategori untuk dropdown - ELOQUENT
        $kategori = KategoriModel::all();

        return view('buku.index', compact('buku', 'kategori', 'totalBuku', 'tersedia', 'habis'));
    }

    /**
     * Tampilkan form tambah buku
     */
    public function tambah()
    {
        // Ambil semua kategori untuk dropdown - ELOQUENT
        $kategori = KategoriModel::all();
        return view('buku.tambah', compact('kategori'));
    }

    /**
     * Simpan buku baru - ELOQUENT
     */
    public function simpan(Request $request)
    {
       $messages = [
            'kode_buku.required' => 'Kode buku harus diisi.',
            'kode_buku.max' => 'Kode buku tidak boleh lebih dari :max karakter.',
            'kode_buku.unique' => 'Kode buku sudah ada.',

            'judul.required' => 'Judul buku harus diisi.',
            'judul.string' => 'Judul buku harus berupa string.',
            'judul.max' => 'Judul buku tidak boleh lebih dari :max karakter.',

            'pengarang.required' => 'Pengarang buku harus diisi.',
            'pengarang.string' => 'Pengarang buku harus berupa string.',
            'pengarang.max' => 'Pengarang buku tidak boleh lebih dari :max karakter.',

            'penerbit.required' => 'Penerbit buku harus diisi.',
            'penerbit.string' => 'Penerbit buku harus berupa string.',
            'penerbit.max' => 'Penerbit buku tidak boleh lebih dari :max karakter.',

            'tahun_terbit.required' => 'Tahun terbit buku harus diisi.',
            'tahun_terbit.numeric' => 'Tahun terbit buku harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit buku harus terdiri dari 4 digit.',

            'deskripsi.required' => 'Deskripsi buku harus diisi.',
            'deskripsi.string' => 'Deskripsi buku harus berupa string.',

            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'File yang diunggah harus berupa jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 3MB.',

            'total_buku.required' => 'Stok buku harus diisi.',
            'total_buku.integer' => 'Stok buku harus berupa angka.',
            'total_buku.min' => 'Stok buku tidak boleh kurang dari 0.',

            'harga_buku.required' => 'Harga buku harus diisi.',
            'harga_buku.integer' => 'Harga buku harus berupa angka bulat.',
            'harga_buku.min' => 'Harga buku tidak boleh kurang dari 0.',

            'kategori_id.required' => 'Kategori buku harus diisi minimal 1.',
            'kategori_id.min' => 'Kategori minimal 1 kategori harus dipilih.',
        ];

        // Validasi input
        $request->validate([
            'kode_buku' => 'required|max:22|unique:buku,kode_buku',
            'judul' => 'required|string|max:60',
            'pengarang' => 'required|string|max:50',
            'penerbit' => 'required|string|max:50',
            'tahun_terbit' => 'required|numeric|digits:4',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048',
            'total_buku' => 'required|integer|min:0',
            'harga_buku' => 'required|integer|min:0',
            'kategori_id' => 'required|min:1',
        ], $messages);

        // Ambil admin yang sedang login - ELOQUENT
        $admin = AdminModel::where('user_id', Auth::id())->first();

        // Bersihkan format harga
        $harga_buku_bersih = (int) preg_replace('/[^0-9]/', '', $request->harga_buku);

        // Buat buku baru - ELOQUENT
        $buku = BukuModel::create([
            'kode_buku' => $request->kode_buku,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
            'deskripsi' => $request->deskripsi,
            'total_buku' => $request->total_buku,
            'stok_buku' => $request->total_buku,
            'harga_buku' => $harga_buku_bersih,
            'id_admin' => $admin?->id,
            'status' => $request->total_buku > 0 ? 'Tersedia' : 'Habis',
        ]);

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nama_file = $buku->id . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('assets/img/buku/'), $nama_file);

            // Update foto buku - ELOQUENT
            $buku->update(['foto' => $nama_file]);
        }

        // Attach kategori buku - ELOQUENT
        if ($request->filled('kategori_id')) {
            $buku->kategori()->attach($request->kategori_id);
        }

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    // Menampilkan detail buku
    public function detail($id)
    {
        $buku = BukuModel::with('kategori')->findOrFail($id);

        // Total stok buku sebelum dikurangi peminjaman (menggunakan kolom total_buku)
        $totalStokBuku = $buku->total_buku;

        // Ambil referensi dari mana pengguna berasal
        $ref = request('ref');
        $kategori_id = request('kategori_id'); // id referensi untuk kembali ke halaman kategori
        $dashboard = request('dashboard'); // id referensi untuk kembali ke halaman dashboard

        // Referensi untuk kembali ke page sebelumnya
        $page = request('page');
        $search = request('search');
        $kategoriFilter = request('kategori');
        $status = request('status');

        return view('buku.detail', compact('buku', 'totalStokBuku', 'ref', 'kategori_id', 'dashboard', 'page', 'search', 'kategoriFilter', 'status'));
    }

    // Menampilkan form edit buku
    public function edit($id)
    {
        $buku = BukuModel::findOrFail($id);
        $kategori = KategoriModel::all();

        // Ambil referensi dari mana pengguna berasal
        $ref = request('ref');
        $kategori_id = request('kategori_id');

        // Referensi untuk kembali ke page sebelumnya
        $page = request('page');
        $search = request('search');
        $kategoriFilter = request('kategori');
        $status = request('status');

        return view('buku.edit', compact('buku', 'kategori', 'ref', 'kategori_id', 'page', 'search', 'kategoriFilter', 'status'));
    }

    // Mengupdate data buku
    public function update(Request $request, $id)
    {
        $messages = [
            'kode_buku.required' => 'Kode buku harus diisi.',
            'kode_buku.max' => 'Kode buku tidak boleh lebih dari :max karakter.',
            'kode_buku.unique' => 'Kode buku sudah ada.',

            'judul.required' => 'Judul buku harus diisi.',
            'judul.string' => 'Judul buku harus berupa string.',
            'judul.max' => 'Judul buku tidak boleh lebih dari :max karakter.',

            'pengarang.required' => 'Pengarang buku harus diisi.',
            'pengarang.string' => 'Pengarang buku harus berupa string.',
            'pengarang.max' => 'Pengarang buku tidak boleh lebih dari :max karakter.',

            'penerbit.required' => 'Penerbit buku harus diisi.',
            'penerbit.string' => 'Penerbit buku harus berupa string.',
            'penerbit.max' => 'Penerbit buku tidak boleh lebih dari :max karakter.',

            'tahun_terbit.required' => 'Tahun terbit buku harus diisi.',
            'tahun_terbit.numeric' => 'Tahun terbit buku harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit buku harus terdiri dari 4 digit.',

            'deskripsi.required' => 'Deskripsi buku harus diisi.',
            'deskripsi.string' => 'Deskripsi buku harus berupa string.',

            'foto.image' => 'File yang diunggah harus berupa gambar.',
            'foto.mimes' => 'File yang diunggah harus berupa jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran file tidak boleh lebih dari 3MB.',

            'total_buku.required' => 'Stok buku harus diisi.',
            'total_buku.integer' => 'Stok buku harus berupa angka.',
            'total_buku.min' => 'Stok buku tidak boleh kurang dari :min.',

            'harga_buku.required' => 'Harga buku harus diisi.',
            'harga_buku.integer' => 'Harga buku harus berupa angka bulat.',
            'harga_buku.min' => 'Harga buku tidak boleh kurang dari 0.',

            'kategori_id.required' => 'Kategori buku harus diisi minimal 1.',
            'kategori_id.min' => 'Kategori minimal 1 kategori harus dipilih.',

        ];

        // Ambil data buku terlebih dahulu
        $buku = BukuModel::findOrFail($id);

        // PENTING: Hitung jumlah buku yang sedang dipinjam dan diproses dari database
        // Ini lebih akurat daripada menghitung dari selisih total dan stok
        $bukuDipinjam = \App\Models\PeminjamanModel::where('buku_id', $id)
            ->whereIn('status', ['Dipinjam', 'Diproses', 'Terlambat'])
            ->count();

        // Tambahkan debug output untuk memverifikasi hasil perhitungan peminjaman aktif
        // dd("Buku dengan ID $id: Total Dipinjam: $bukuDipinjam");

        // Jika ada buku yang sedang dipinjam, kita perlu memastikan total buku tidak kurang dari itu
        if ($bukuDipinjam > 0) {
            // Update pesan error untuk validasi total_buku
            $messages['total_buku.min'] = "Total buku minimal harus minimal $bukuDipinjam. Karena saat ini ada $bukuDipinjam buku sedang dipinjam.";
        }

        // Validasi input dengan tambahan validasi untuk total_buku
        // total_buku minimal harus sama dengan jumlah buku yang sedang dipinjam
        $request->validate([
            'kode_buku' => 'required|max:22|unique:buku,kode_buku,' . $id,
            'judul' => 'required|string|max:60',
            'pengarang' => 'required|string|max:50',
            'penerbit' => 'required|string|max:50',
            'tahun_terbit' => 'required|numeric|digits:4',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3048',
            'total_buku' => 'required|integer|min:' . $bukuDipinjam, // Kunci perbaikan ada di sini
            'harga_buku' => 'required|integer|min:0',
            'kategori_id' => 'required|min:1',
        ], $messages);

        // Ambil data admin yang sedang login dan update id_admin pada tabel buku
        $adminModel = AdminModel::where('user_id', Auth::id())->first();

        // Bersihkan format harga buku dari prefix Rp dan pemisah ribuan
        $harga_buku_bersih = preg_replace('/[^0-9]/', '', $request->harga_buku);
        $harga_buku_bersih = intval($harga_buku_bersih);

        $buku->kode_buku = $request->kode_buku;
        $buku->judul = $request->judul;
        $buku->pengarang = $request->pengarang;
        $buku->penerbit = $request->penerbit;
        $buku->tahun_terbit = $request->tahun_terbit;
        $buku->deskripsi = $request->deskripsi;
        $buku->harga_buku = $harga_buku_bersih;

        // PERBAIKAN: Dapatkan total buku baru dari request dan pastikan dikonversi ke integer
        // untuk menghindari masalah tipe data
        $newTotal = (int)$request->total_buku;

        // Verifikasi bahwa nilai total valid - total tidak boleh kurang dari jumlah yang dipinjam
        if ($newTotal < $bukuDipinjam) {
            $newTotal = $bukuDipinjam;
        }

        // Update total_buku dengan nilai input yang baru
        $buku->total_buku = $newTotal;

        // Update id_admin berdasarkan admin yang sedang melakukan edit
        if ($adminModel) {
            $buku->id_admin = $adminModel->id;
        }

        // RUMUS UTAMA: stok_buku = total_buku - bukuDipinjam
        // Hitung stok buku yang tersedia (total buku dikurangi jumlah yang sedang dipinjam)
        $buku->stok_buku = max(0, $newTotal - $bukuDipinjam);

        // Buku sudah disiapkan dengan total_buku dan stok_buku yang benar

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($buku->foto && file_exists(public_path('assets/img/buku/' . $buku->foto))) {
                // Hapus foto lama
                unlink(public_path('assets/img/buku/' . $buku->foto));
            }
            // Upload foto baru
            $foto = $request->file('foto');
            $nama_file = $buku->id . '_' . $foto->getClientOriginalName(); // Menggunakan ID buku untuk menghindari duplikasi
            $foto->move(public_path('assets/img/buku/'), $nama_file);
            $buku->foto = $nama_file;
        }

        // Final check: pastikan stok dihitung dengan benar
        $buku->stok_buku = max(0, $buku->total_buku - $bukuDipinjam);

        // Set status berdasarkan stok, jika <= 0, set status "Habis" jika >0, set status "Tersedia"
        if ($buku->stok_buku <= 0) {
            $buku->status = 'Habis';
        } else {
            $buku->status = 'Tersedia';
        }

        // Simpan perubahan
        $buku->save();

        // Tambahkan baris ini untuk update kategori
        if ($request->has('kategori_id')) {
            $buku->kategori()->sync($request->kategori_id);
        }

        // Cek apakah ada referensi ke halaman kategori
        if ($request->has('ref') && $request->ref == 'kategori' && $request->has('kategori_id')) {
            // Referensi untuk kembali ke halaman kategori
            $page = $request->input('page');
            $search = $request->input('search');
            $kategori_id = $request->input('kategori_id');
            // Handle if kategori_id agar dibaca berupa array
            if (is_array($kategori_id)) {
                $kategori_id = reset($kategori_id); // Ambil kategori pertama dari array
            }

            // Pesan sukses dengan detail stok yang lebih jelas
            $successMessage = 'Buku berhasil diperbarui. Total buku: ' . $buku->total_buku .
                ', Stok tersedia: ' . $buku->stok_buku .
                ($bukuDipinjam > 0 ? ', Dipinjam: ' . $bukuDipinjam : '') .
                ' (Input total buku: ' . $request->total_buku . ')';

            return redirect()->route('kategori.detail', [
                'id' => $kategori_id,
                'page' => $page ?? '',
                'search' => $search ?? '',
            ])->with('success', $successMessage);
        } else {

            // Referensi untuk kembali ke page sebelumnya - menggunakan parameter yang sama dengan tombol "Kembali"
            $page = $request->input('page');
            $search = $request->input('search');
            $kategoriFilter = $request->input('kategori');
            $status = $request->input('status');

            // Pesan sukses dengan detail stok yang lebih jelas
            $successMessage = 'Buku berhasil diperbarui. Total buku: ' . $buku->total_buku .
                ', Stok tersedia: ' . $buku->stok_buku .
                ($bukuDipinjam > 0 ? ', Dipinjam: ' . $bukuDipinjam : '') .
                ' (Input total buku: ' . $request->total_buku . ')';

            // Gunakan parameter yang sama persis dengan tombol "Kembali" di view
            return redirect()->route('buku.index', [
                'page' => $page ?? '',
                'search' => $search ?? '',
                'kategori' => $kategoriFilter ?? '',
                'status' => $status ?? ''
            ])->with('success', $successMessage);
        }
    }

    // Menghapus data buku
    public function hapus($id)
    {
        $buku = BukuModel::findOrFail($id);
        // Hapus foto jika ada
        if ($buku->foto && file_exists(public_path('assets/img/buku/' . $buku->foto))) {
            unlink(public_path('assets/img/buku/' . $buku->foto));
        }
        $buku->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    // Menggunakan library simple-qrcode dan extension imagick (untuk membaca qr code)
    // Download QR Code
    public function downloadQrCode($id)
    {
        $buku = BukuModel::findOrFail($id);
        $filename = 'qrcode-' . $buku->kode_buku . '.png';

        // Path ke logo yang akan ditempatkan di tengah QR code
        $logoPath = public_path('assets/img/logo_mts.png');

        // Generate QR code dalam format png dengan logo di tengah
        $qrcode = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H') // Error correction level tinggi untuk memastikan QR code masih bisa terbaca meski ada logo
            ->merge($logoPath, 0.3, true) // Menambahkan logo dengan ukuran 30% dari QR code
            ->generate(route('peminjaman.form', $buku->id));

        // Return file sebagai download
        return response($qrcode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }



    // Method untuk mengambil semua buku untuk ekspor
    public function getAllBooksForExport(Request $request)
    {
        // Ambil parameter filter dan pencarian
        $kategoriId = $request->get('kategori');
        $status = $request->get('status');
        $search = $request->get('search');

        // Base query untuk ngeloading data buku dengan relasinya
        $query = BukuModel::with('kategori');

        // Terapkan filter kategori jika ada
        if ($kategoriId) {
            $query->whereHas('kategori', function ($q) use ($kategoriId) {
                $q->where('kategori.id', $kategoriId);
            });
        }

        // Terapkan filter status jika ada
        if ($status) {
            $query->where('status', $status);
        }

        // Terapkan pencarian judul jika ada
        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%');
        }

        // Ambil semua buku yang sesuai dengan filter (tanpa pagination)
        $buku = $query->get();

        // Format data untuk respons
        $formattedBooks = $buku->map(function ($book) {
            return [
                'kode_buku' => $book->kode_buku,
                'judul' => $book->judul,
                'pengarang' => $book->pengarang,
                'penerbit' => $book->penerbit,
                'tahun_terbit' => $book->tahun_terbit,
                'kategori' => $book->kategori->pluck('nama_kategori')->implode(', '),
                'total_buku' => $book->total_buku,
                'stok_buku' => $book->stok_buku,
                'status' => $book->status,
                'deskripsi' => $book->deskripsi,
                'harga_buku' => $book->harga_buku,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBooks
        ]);
    }
}
