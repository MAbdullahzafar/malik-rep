<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds to create the system administrator.
     */
    public function run(): void
    {
        // Drop any matching email entries to prevent duplication errors
        DB::table('users')->where('email', 'malikabdullahbinzafar@gmail.com')->delete();

        // Inject your clean administrator profile record natively
        DB::table('users')->insert([
            'name' => 'Muhammad Abdullah Zafar',
            'email' => 'malikabdullahbinzafar@gmail.com',
            'password' => Hash::make('SecureAdmin2026!'), // 👈 Sets your active login password
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}