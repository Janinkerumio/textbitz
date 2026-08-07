<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@textbitz.com',
            'phone_number' => '+639171234567',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
