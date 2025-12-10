<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Create the default admin account.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Ecoly',
            'email' => 'admin@ecoly.mr',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'phone' => '+222 00 00 00 00',
            'is_active' => true,
        ]);
    }
}
