<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Creates your permanent Admin account with a securely hashed password
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@alphv.com',
            'password' => Hash::make('admin'), 
        ]);
    }
}