<?php

namespace Database\Seeders;

use App\Models\User;
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

        User::factory()->create([
            'username' => 'TestUser',       // Use the correct column
            'userEmail' => 'test@example.com', // Use the correct column
            'userPassword' => bcrypt('password'), // Hashed password
            'userFullName' => 'Test User',
        ]);
    }
}
