<?php

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Carbon;
use function Pest\Laravel\{actingAs, postJson};

it('allows donation on active campaign', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
    ]);

    actingAs($user, 'sanctum');

    $response = postJson('/api/donations', [
        'campaign_id' => $campaign->id,
        'amount' => 150,
        'currency' => 'EUR',
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('data.amount', 150)
             ->assertJsonPath('data.campaign_id', $campaign->id);
});

it('rejects donation on upcoming campaign', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
    ]);

    actingAs($user, 'sanctum');

    $response = postJson('/api/donations', [
        'campaign_id' => $campaign->id,
        'amount' => 100,
        'currency' => 'EUR',
    ]);

    $response->assertStatus(400)
             ->assertJsonPath('message', 'You can only donate to active campaigns.');
});

it('rejects donation on ended campaign', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(1),
    ]);

    actingAs($user, 'sanctum');

    $response = postJson('/api/donations', [
        'campaign_id' => $campaign->id,
        'amount' => 50,
        'currency' => 'EUR',
    ]);

    $response->assertStatus(400)
             ->assertJsonPath('message', 'You can only donate to active campaigns.');
});

it('blocks unauthenticated donation', function () {
    $campaign = Campaign::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
    ]);

    $response = postJson('/api/donations', [
        'campaign_id' => $campaign->id,
        'amount' => 99,
        'currency' => 'USD',
    ]);

    $response->assertStatus(401);
});
