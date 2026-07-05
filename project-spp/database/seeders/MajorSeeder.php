<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            [
                'code'        => 'RPL',
                'name'        => 'Rekayasa Perangkat Lunak',
                'description' => 'Jurusan yang mempelajari pengembangan perangkat lunak.',
                'is_active'   => true,
            ],
            [
                'code'        => 'TKJ',
                'name'        => 'Teknik Komputer dan Jaringan',
                'description' => 'Jurusan yang mempelajari jaringan komputer dan sistem operasi.',
                'is_active'   => true,
            ],
        ];

        foreach ($majors as $major) {
            // FIX: Key pakai 'code' (unik & stabil) — bukan 'name'
            // Jika nama jurusan berubah, updateOrCreate tetap menemukan record
            // yang benar dan update, bukan membuat record baru
            Major::updateOrCreate(
                ['code' => $major['code']],
                $major
            );
        }
    }
}
