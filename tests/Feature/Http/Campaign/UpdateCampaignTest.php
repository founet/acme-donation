<?php

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Carbon;
use function Pest\Laravel\{actingAs, putJson};

it('updates campaign if user is creator and campaign not started', function () {
    $user = User::factory()->create();

    $campaign = Campaign::factory()->create([
        'creator_id' => $user->id,
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
    ]);

    actingAs($user, 'sanctum');

    $response = putJson("/api/campaigns/{$campaign->id}", [
        'title' => 'Updated Title',
        'goal_amount' => 9999,
        'description' => 'Updated description',
        'start_date' => now()->addDays(3)->toDateString(),
        'end_date' => now()->addDays(12)->toDateString(),
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Updated Title');
});

it('blocks update if campaign already started', function () {
    $user = User::factory()->create();

    $campaign = Campaign::factory()->create([
        'creator_id' => $user->id,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(5),
    ]);

    actingAs($user, 'sanctum');

    $response = putJson("/api/campaigns/{$campaign->id}", [
        'title' => 'New Title',
        'goal_amount' => 10000,
        'description' => 'New description',
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(8)->toDateString(),
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('message', "Can't modify campaign dates once it has started.");
});

it('blocks update if user is not the creator', function () {
    $creator = User::factory()->create();
    $other = User::factory()->create();

    $campaign = Campaign::factory()->create([
        'creator_id' => $creator->id,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(5),
    ]);

    actingAs($other, 'sanctum');

    $response = putJson("/api/campaigns/{$campaign->id}", [
        'title' => 'Stolen Update',
        'description' => 'This is a stolen update.',
        'goal_amount' => 10000,
    ]);

    $response->assertStatus(400);
});
