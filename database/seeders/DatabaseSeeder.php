<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@admin.com'], // lookup key
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'password' => Hash::make('password'), // change to a secure password
                'job_type' => 'Administrator',
                'employment_status' => 'Full-time',
                'hourly_rate' => 0,
                'is_admin' => true,
                'is_active' => true,
            ]
        );
    }
}
