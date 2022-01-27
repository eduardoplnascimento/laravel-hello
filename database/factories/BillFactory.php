<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'invoice' => $this->faker->randomNumber(4),
            'installment' => $this->faker->randomNumber(1),
            'client_id' => Client::factory()->create()->id,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'payment_date' => $this->faker->dateTimeBetween('-1 week')
        ];
    }
}
