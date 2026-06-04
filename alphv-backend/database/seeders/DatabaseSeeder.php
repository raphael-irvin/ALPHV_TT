<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed the admin account (idempotent — safe to run multiple times)
        User::firstOrCreate(
            ['email' => 'admin@alphv.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin'),
            ]
        );

        // 2. Seed fake records for development and pagination testing
        $this->call(RecordSeeder::class);
    }
}