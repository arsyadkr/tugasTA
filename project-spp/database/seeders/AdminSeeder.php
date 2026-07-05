<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Key: email — field unik yang stabil, tidak akan berubah
        User::updateOrCreate(
            ['email' => 'admin@spp.test'],
            [
                'name'                 => 'Administrator',
                'username'             => null,
                'password'             => Hash::make('admin'),
                'role'                 => 'admin',
                'must_change_password' => false,
            ]
        );
    }
}
