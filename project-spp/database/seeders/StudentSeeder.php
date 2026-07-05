<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Resolve ID via field unik yang stabil — tidak hardcode integer ID
        $classIds = SchoolClass::whereIn('name', ['X RPL 1', 'X RPL 2', 'X TKJ 1'])
            ->pluck('id', 'name');

        $majorIds = Major::whereIn('code', ['RPL', 'TKJ'])
            ->pluck('id', 'code');

        $students = [
            [
                'nis'      => '2024001',
                'name'     => 'Andi Pratama',
                'gender'   => 'L',
                'class'    => 'X RPL 1',
                'major'    => 'RPL',
                'phone'    => '081234567001',
                'address'  => 'Jl. Merdeka No. 1, Jakarta',
            ],
            [
                'nis'      => '2024002',
                'name'     => 'Siti Rahayu',
                'gender'   => 'P',
                'class'    => 'X RPL 1',
                'major'    => 'RPL',
                'phone'    => '081234567002',
                'address'  => 'Jl. Sudirman No. 2, Jakarta',
            ],
            [
                'nis'      => '2024003',
                'name'     => 'Budi Santoso',
                'gender'   => 'L',
                'class'    => 'X RPL 2',
                'major'    => 'RPL',
                'phone'    => '081234567003',
                'address'  => 'Jl. Gatot Subroto No. 3, Bandung',
            ],
            [
                'nis'      => '2024004',
                'name'     => 'Dewi Lestari',
                'gender'   => 'P',
                'class'    => 'X TKJ 1',
                'major'    => 'TKJ',
                'phone'    => '081234567004',
                'address'  => 'Jl. Ahmad Yani No. 4, Surabaya',
            ],
            [
                'nis'      => '2024005',
                'name'     => 'Rizky Firmansyah',
                'gender'   => 'L',
                'class'    => 'X TKJ 1',
                'major'    => 'TKJ',
                'phone'    => '081234567005',
                'address'  => 'Jl. Diponegoro No. 5, Semarang',
            ],
        ];

        // FIX PERFORMANCE: Satu transaction untuk semua siswa
        // Lebih cepat dan lebih aman — rollback semua jika ada yang gagal
        DB::transaction(function () use ($students, $classIds, $majorIds) {
            foreach ($students as $data) {

                // FIX IDEMPOTENT: Key pakai 'nis' — field unik yang stabil
                // Jika nama siswa berubah, record tetap ditemukan via NIS
                if (Student::where('nis', $data['nis'])->exists()) {
                    continue;
                }

                // Buat user — username = NIS, password = Hash(NIS)
                $user = User::create([
                    'name'                 => $data['name'],
                    'username'             => $data['nis'],
                    'email'                => null,
                    // FIX PASSWORD: Hash::make wajib — jangan store plain text
                    'password'             => Hash::make($data['nis']),
                    'role'                 => 'student',
                    'must_change_password' => true,
                ]);

                // Buat profil siswa — resolve class_id dan major_id dari nama
                Student::create([
                    'user_id'  => $user->id,
                    'nis'      => $data['nis'],
                    'name'     => $data['name'],
                    'gender'   => $data['gender'],
                    'class_id' => $classIds[$data['class']],
                    'major_id' => $majorIds[$data['major']],
                    'phone'    => $data['phone'],
                    'address'  => $data['address'],
                ]);
            }
        });
    }
}
