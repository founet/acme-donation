<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use function Pest\Laravel\{actingAs, getJson};

it('returns only donations from authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $campaign = Campaign::factory()->create([
        'start_date' => now()->subDays(2),
        'end_date' => now()->addDays(5),
    ]);

    // Donations du user connecté
    Donation::factory()->create([
        'employee_id' => $user->id,
        'campaign_id' => $campaign->id,
        'amount' => 50
    ]);

    // ❌ Donation d’un autre utilisateur
    Donation::factory()->create([
        'employee_id' => $otherUser->id,
        'campaign_id' => $campaign->id,
        'amount' => 200
    ]);

    actingAs($user, 'sanctum');

    $response = getJson('/api/me/donations');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.amount', 50);
});

it('blocks access if not authenticated', function () {
    $response = getJson('/api/me/donations');
    $response->assertStatus(401);
});
