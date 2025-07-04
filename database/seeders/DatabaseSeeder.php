<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserModel::factory(10)->create();

        // UserModel::factory()->create([
        //     'nama' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => bcrypt('password123'),
        //     'level' => 'admin',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'is_active' => true,
        // ]);
    }
}
