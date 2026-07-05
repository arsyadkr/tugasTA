<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan wajib mengikuti dependency chain:
     *
     *   AdminSeeder          → users (admin)
     *   MajorSeeder          → majors
     *   ClassSeeder          → classes         (requires: majors)
     *   StudentSeeder        → users + students (requires: classes, majors)
     *   BillAndPaymentSeeder → bills + payments (requires: students)
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MajorSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
            BillAndPaymentSeeder::class,
        ]);
    }
}
