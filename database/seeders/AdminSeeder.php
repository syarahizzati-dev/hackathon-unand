<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed admin users.
     */
    public function run(): void
    {
        // Admin 1 — Psikolog utama
        User::updateOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'nama'           => 'Dr. Sarah Wijaya',
                'nim'            => null,
                'email'          => 'admin@kampus.ac.id',
                'no_telepon'     => '081234567800',
                'jurusan'        => null,
                'program_studi'  => null,
                'kontak_darurat' => null,
                'password'       => Hash::make('admin123'),
                'username_anonim' => 'Admin',
                'is_admin'       => true,
            ]
        );

        // Admin 2 — Konselor tambahan
        User::updateOrCreate(
            ['email' => 'konselor@kampus.ac.id'],
            [
                'nama'           => 'Dr. Budi Hartono',
                'nim'            => null,
                'email'          => 'konselor@kampus.ac.id',
                'no_telepon'     => '081234567801',
                'jurusan'        => null,
                'program_studi'  => null,
                'kontak_darurat' => null,
                'password'       => Hash::make('admin123'),
                'username_anonim' => 'Konselor',
                'is_admin'       => true,
            ]
        );
    }
}
