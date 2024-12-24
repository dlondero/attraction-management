<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attraction;

class AttractionSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 random attractions
        Attraction::factory()->count(20)->create();

        // Create some specific attractions
        $specificAttractions = [
            [
                'name' => 'Eiffel Tower',
                'description' => 'Iconic iron lattice tower on the Champ de Mars in Paris.',
                'location' => 'Paris, France',
                'price' => 25.00,
                'image' => null,
            ],
            [
                'name' => 'Grand Canyon',
                'description' => 'Steep-sided canyon carved by the Colorado River in Arizona.',
                'location' => 'Arizona, USA',
                'price' => 35.00,
                'image' => null,
            ],
            [
                'name' => 'Great Wall of China',
                'description' => 'Series of fortifications and walls across the historical northern borders of ancient Chinese states.',
                'location' => 'China',
                'price' => 40.00,
                'image' => null,
            ],
        ];

        foreach ($specificAttractions as $attraction) {
            Attraction::create($attraction);
        }
    }
}
