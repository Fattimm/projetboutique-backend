<?php

namespace Database\Factories;

use App\Models\Dette;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetteFactory extends Factory
{
    protected $model = Dette::class;

    public function definition()
    {
        return [
            'client_id' => Client::inRandomOrder()->first()->id,
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 2000),
            'montantDu' => $this->faker->randomFloat(2, 0, 2000),
            'montantRestant' => $this->faker->randomFloat(2, 0, 2000),
        ];
    }

}