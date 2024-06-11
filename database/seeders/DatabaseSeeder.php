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

        User::create([
            'name' => 'Mauricio Goes',
            'email' => 'mauricio@mgpsistemas.com.br',
            'password' => bcrypt('change-me'),
            'is_admin' => true,
            'is_active' => true,
            'approved_at' => now(),
        ]);
    }
}
