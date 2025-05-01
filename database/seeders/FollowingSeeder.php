<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Creator;
use App\Models\Following;


class FollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creators = Creator::all();

        foreach ($creators as $creator) {
            // Each creator follows 2-5 people
            $followings = $creators->random(rand(2, 5));

            foreach ($followings as $follow) {
                if ($creator->id !== $follow->id) { // cannot follow themselves
                    Following::firstOrCreate([
                        'cre_id' => $creator->id,
                        'whom' => $follow->id,
                    ]);
                }
            }
        }
    }
}
