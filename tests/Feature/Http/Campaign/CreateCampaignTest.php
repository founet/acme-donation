<?php

use App\Models\User;
use Illuminate\Support\Facades\Date;
use function Pest\Laravel\{postJson, actingAs};

it('allows an authenticated user to create a campaign', function () {
    $user = User::factory()->create();

    $payload = [
        'title' => 'Plant Trees',
        'description' => 'Let’s make the Earth greener',
        'goal_amount' => 10000,
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(10)->toDateString(),
    ];

    actingAs($user, 'sanctum');

    $response = postJson('/api/campaigns', $payload);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Plant Trees');
});

it('rejects unauthenticated access', function () {
    $response = postJson('/api/campaigns', []);
    $response->assertStatus(401);
});

it('validates start_date and end_date logic', function () {
    $user = User::factory()->create();

    $payload = [
        'title' => 'Bad Dates',
        'description' => 'Oops',
        'goal_amount' => 1000,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(3)->toDateString(), // ❌ end before start
    ];

    actingAs($user, 'sanctum');

    $response = postJson('/api/campaigns', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_date']);
});
