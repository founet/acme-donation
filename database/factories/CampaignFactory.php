<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->text(30),
            'description' => $this->faker->paragraph,
            'goal_amount' => $this->faker->numberBetween(1000, 100000),
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(),
            'creator_id' => 1,
        ];
    }
}
