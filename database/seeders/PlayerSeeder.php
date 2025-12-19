<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 14 random players with user accounts
        // Each player will have a unique rank from 1 to 14
        for ($rank = 1; $rank <= 14; $rank++) {
            Player::factory()
                ->rank($rank)
                ->create();
        }
    }
}
