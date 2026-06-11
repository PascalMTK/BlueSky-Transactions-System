<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'name' => 'Super Administrateur',
            'email' => 'admin@bluesky.com',
            'password' => Hash::make('Admin@2024!'),
            'phone' => '+000000000000',
            'role' => 'admin',
            'country_id' => null,
            'agent_code' => 'BSK-ADMIN-001',
            'status' => 'active',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
