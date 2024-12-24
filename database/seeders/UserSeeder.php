<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin user
        User::factory()
            ->withRole('admin')
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);

        // Create a regular user
        User::factory()
            ->withRole('user')
            ->create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
            ]);
    }
}
