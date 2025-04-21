<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\IdentifikasiObservasi;

class IdentifikasiObservasiFactory extends Factory
{
    protected $model = IdentifikasiObservasi::class;

    public function definition()
    {
        return [
            'species_name' => $this->faker->word,
            'common_name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'observed_at' => $this->faker->date,
            'uploaded_at' => $this->faker->date,
            'rating' => $this->faker->numberBetween(1, 5),
            'ratings_count' => $this->faker->numberBetween(1, 100),
            'source' => $this->faker->word,
        ];
    }
}
