<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $adminId = DB::table('users')->insertGetId([
            'nama' => 'Admin Perpustakaan',
            'email' => 'admin@perpustakaan.com',
            'password' => Hash::make('password'),
            'level' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('admin')->insert([
            'user_id' => $adminId,
            'nip' => '123456789',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Admin Perpustakaan No. 1',
            'no_telepon' => '081234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Siswa
        $siswaId = DB::table('users')->insertGetId([
            'nama' => 'Siswa Perpustakaan',
            'email' => 'siswa@perpustakaan.com',
            'password' => Hash::make('password'),
            'level' => 'siswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('siswa')->insert([
            'user_id' => $siswaId,
            'nis' => '2025001',
            'kelas' => '9A',
            'tanggal_lahir' => '2005-01-01',
            'alamat' => 'Jl. Siswa Perpustakaan No. 1',
            'no_telepon' => '081234567891',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guru
        $guruId = DB::table('users')->insertGetId([
            'nama' => 'Guru Perpustakaan',
            'email' => 'guru@perpustakaan.com',
            'password' => Hash::make('password'),
            'level' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('guru')->insert([
            'user_id' => $guruId,
            'nip' => '987654321',
            'mata_pelajaran' => 'Bahasa Indonesia',
            'tanggal_lahir' => '1985-01-01',
            'alamat' => 'Jl. Guru Perpustakaan No. 1',
            'no_telepon' => '081234567892',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff
        $staffId = DB::table('users')->insertGetId([
            'nama' => 'Staff Perpustakaan',
            'email' => 'staff@perpustakaan.com',
            'password' => Hash::make('password'),
            'level' => 'staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('staff')->insert([
            'user_id' => $staffId,
            'nip' => '1122334455',
            'bagian' => 'Tata Usaha',
            'tanggal_lahir' => '1992-01-01',
            'alamat' => 'Jl. Staff Perpustakaan No. 1',
            'no_telepon' => '081234567893',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
