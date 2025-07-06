<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use App\Models\AdminModel;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    // ELOQUENT - Menampilkan semua kategori
    public function index()
    {
        $kategori = KategoriModel::all();
        return view('kategori.index', compact('kategori'));
    }

    // ELOQUENT - Menampilkan detail kategori dan buku terkait
    public function detail($id)
    {
        // ELOQUENT - Ambil detail kategori berdasarkan ID
        $kategori = KategoriModel::findOrFail($id);

        // ELOQUENT - Ambil buku yang terkait dengan pagination dan pencarian
        $search = request('search');
        $query = $kategori->buku();

        // Filter berdasarkan pencarian jika ada
        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%');
        }

        // Pagination
        $bukuKategori = $query->paginate(8);
        $bukuKategori->appends(request()->query());

        return view('kategori.detail', compact('kategori', 'bukuKategori'));
    }

    // Menampilkan form tambah kategori
    public function tambah()
    {
        return view('kategori.tambah');
    }

    // ELOQUENT - Menyimpan kategori baru
    public function simpan(Request $request)
    {
        $messages = [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.string' => 'Nama kategori harus berupa string',
            'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari :max karakter',
            'nama_kategori.unique' => 'Nama kategori sudah ada',

            'deskripsi.string' => 'Deskripsi kategori harus berupa string',
        ];

        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:30|unique:kategori,nama_kategori',
            'deskripsi' => 'nullable|string',
        ], $messages);

        // ELOQUENT - Ambil data admin yang sedang login
        $adminModel = AdminModel::where('user_id', Auth::id())->first();

        // Siapkan data kategori
        $kategoriData = [
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ];

        // Set id_admin berdasarkan admin yang sedang login
        if ($adminModel) {
            $kategoriData['id_admin'] = $adminModel->id;
        }

        // ELOQUENT - Simpan kategori baru
        KategoriModel::create($kategoriData);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    // ELOQUENT - Menampilkan form edit kategori
    public function edit($id)
    {
        // ELOQUENT - Ambil data kategori berdasarkan ID
        $kategori = KategoriModel::findOrFail($id);

        // Ambil parameter referensi
        $ref = request('ref');

        return view('kategori.edit', compact('kategori', 'ref'));
    }

    // ELOQUENT - Memperbarui data kategori
    public function update(Request $request, $id)
    {
        $messages = [
            'nama_kategori.required' => 'Nama kategori harus diisi',
            'nama_kategori.string' => 'Nama kategori harus berupa string',
            'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari :max karakter',
            'nama_kategori.unique' => 'Nama kategori sudah ada',
            'deskripsi.string' => 'Deskripsi kategori harus berupa string',
        ];

        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:30|unique:kategori,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string',
        ], $messages);

        // ELOQUENT - Ambil data admin yang sedang login
        $adminModel = AdminModel::where('user_id', Auth::id())->first();

        // ELOQUENT - Update kategori
        $kategori = KategoriModel::findOrFail($id);
        $updateData = [
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ];

        // Update id_admin berdasarkan admin yang sedang melakukan edit
        if ($adminModel) {
            $updateData['id_admin'] = $adminModel->id;
        }

        $kategori->update($updateData);

        // Redirect berdasarkan referensi
        if ($request->has('ref') && $request->ref == 'detail') {
            return redirect()->route('kategori.detail', $id)->with('success', 'Kategori berhasil diperbarui');
        } else {
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui');
        }
    }

    // ELOQUENT - Menghapus kategori
    public function hapus($id)
    {
        // ELOQUENT - Hapus kategori berdasarkan ID
        $kategori = KategoriModel::findOrFail($id);

        // ELOQUENT - Cek apakah kategori masih digunakan oleh buku
        if ($kategori->buku->count() > 0) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
