<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Creator;
use App\Models\Follower;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creators = Creator::all();

        foreach ($creators as $creator) {
            // Each creator has 2-5 followers
            $followers = $creators->random(rand(2, 5));

            foreach ($followers as $follower) {
                if ($creator->id !== $follower->id) { // cannot follow themselves
                    Follower::firstOrCreate([
                        'cre_id' => $creator->id,
                        'follower' => $follower->id,
                    ]);
                }
            }
        }
    }
}
