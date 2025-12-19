<?php

namespace Database\Seeders;

use App\Models\ResultDescription;
use Illuminate\Database\Seeder;

class ResultDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $descriptions = [
            ['id' => 1, 'description' => 'beat'],
            ['id' => 2, 'description' => 'drew with'],
            ['id' => 3, 'description' => 'lost to'],
        ];

        foreach ($descriptions as $description) {
            ResultDescription::updateOrCreate(
                ['id' => $description['id']],
                $description
            );
        }
    }
}
