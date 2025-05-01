<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Creator;
use Illuminate\Support\Facades\Hash;

class CreatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create 100 fake creators
         for ($i = 1; $i <= 100; $i++) {
            Creator::create([
                'name' => "Creator $i",
                'username' => "creator$i",
                'email' => "creator$i@example.com",
                'phone' => "123456789$i",
                'password' => Hash::make('password'), // Always hash passwords
                'address' => "Address $i",
                'city' => "City $i",
                'profile_photo' => 'default.jpg',
                'relationship_status' => 'Single',
                'bio' => "This is bio for Creator $i",
            ]);
        }
    }
}
