<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void

    {
        User::firstOrCreate( // firstOrCreate will check if a user with the given email exists, and if not, it will create one
            ['email' => 'admin@alphv.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin'), // Hash::make will hash the password before storing it in the database
            ]
        );
    }
}