<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID jurusan via code — field unik yang stabil
        $rplId = Major::where('code', 'RPL')->value('id');
        $tkjId = Major::where('code', 'TKJ')->value('id');

        $classes = [
            [
                'major_id'      => $rplId,
                'grade'         => 10,
                'name'          => 'X RPL 1',
                'academic_year' => '2024/2025',
                'is_active'     => true,
            ],
            [
                'major_id'      => $rplId,
                'grade'         => 10,
                'name'          => 'X RPL 2',
                'academic_year' => '2024/2025',
                'is_active'     => true,
            ],
            [
                'major_id'      => $tkjId,
                'grade'         => 10,
                'name'          => 'X TKJ 1',
                'academic_year' => '2024/2025',
                'is_active'     => true,
            ],
        ];

        foreach ($classes as $class) {
            // FIX: Key pakai composite [name + academic_year] — keduanya
            // membentuk unique constraint di migration, jadi ini identifier yang tepat
            SchoolClass::updateOrCreate(
                [
                    'name'          => $class['name'],
                    'academic_year' => $class['academic_year'],
                ],
                $class
            );
        }
    }
}
