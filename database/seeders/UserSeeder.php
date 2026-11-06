<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sekolahs = Sekolah::orderBy('id')->take(2)->get();

        User::updateOrCreate(
            ['email' => 'admin@sig.test'],
            [
                'name' => 'Administrator',
                'password' => 'password123',
                'role' => Role::ADMIN->value,
                'sekolah_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dinas@sig.test'],
            [
                'name' => 'Dinas Pendidikan',
                'password' => 'password123',
                'role' => Role::DINAS->value,
                'sekolah_id' => null,
            ]
        );

        if ($sekolahs->count() >= 2) {
            User::updateOrCreate(
                ['email' => 'sekolah1@sig.test'],
                [
                    'name' => 'Operator Sekolah 1',
                    'password' => 'password123',
                    'role' => Role::SEKOLAH->value,
                    'sekolah_id' => $sekolahs[0]->id,
                ]
            );

            User::updateOrCreate(
                ['email' => 'sekolah2@sig.test'],
                [
                    'name' => 'Operator Sekolah 2',
                    'password' => 'password123',
                    'role' => Role::SEKOLAH->value,
                    'sekolah_id' => $sekolahs[1]->id,
                ]
            );
        } else {
            $this->command->warn(
                'Data sekolah belum mencukupi. Akun operator sekolah belum dibuat.'
            );
        }
    }
}
