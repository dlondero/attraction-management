<?php

namespace Database\Factories;

use App\Models\Attraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionFactory extends Factory
{
    protected $model = Attraction::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(3),
            'location' => $this->faker->city,
            'price' => $this->faker->randomFloat(0, 10, 200),
            'image' => null,
        ];
    }
}
