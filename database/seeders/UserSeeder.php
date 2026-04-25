<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ramahindonesia.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Silakan simpan password default Anda di sini
                'is_admin' => true,
            ]
        );
    }
}
