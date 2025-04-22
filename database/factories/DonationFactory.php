<?php


namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(10, 500),
            'currency' => 'EUR',
            'employee_id' => 1,
            'campaign_id' => 1,
            'status' => 'confirmed',
        ];
    }
}
