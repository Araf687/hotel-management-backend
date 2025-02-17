<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;

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
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
        ]);
        Property::create([
            'name' => 'Admin Property',
            'address' => '123 Admin Street, Cityville',
            'image' => 'default_image.jpg', // Adjust as per your needs
            'available_rooms' => 10,
            'per_night_cost' => 3000.00,
            'average_rating' => 4.5,
            'description' => 'This is the default admin property.',
        ]);
    }
}
