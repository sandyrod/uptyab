<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\MunicipiosVenezuelaSeeder;
use Database\Seeders\VenezuelaStatesSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Run seeders
        $this->call([
            VenezuelaStatesSeeder::class,
            MunicipiosVenezuelaSeeder::class,
        ]);
    }
}
