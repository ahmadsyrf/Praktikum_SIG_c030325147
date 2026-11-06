<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Membuat data sekolah terlebih dahulu
        $this->call(SekolahSeeder::class);

        // Membuat akun berdasarkan role
        $this->call(UserSeeder::class);
    }
}