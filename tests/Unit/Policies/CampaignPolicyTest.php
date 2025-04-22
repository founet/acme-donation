<?php

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;

it('allows creator to update or delete campaign', function () {
    $creator = User::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $creator->id]);

    $policy = new CampaignPolicy();

    expect($policy->update($creator, $campaign))->toBeTrue();
    expect($policy->delete($creator, $campaign))->toBeTrue();
});

it('denies update/delete for non-owner user', function () {
    $creator = User::factory()->create();
    $other = User::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $creator->id]);

    $policy = new CampaignPolicy();

    expect($policy->update($other, $campaign))->toBeFalse();
    expect($policy->delete($other, $campaign))->toBeFalse();
});

it('allows admin to access anything', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $campaign = Campaign::factory()->create();

    $policy = new CampaignPolicy();

    expect($policy->viewDonations($admin, $campaign))->toBeTrue();
    expect($policy->update($admin, $campaign))->toBeTrue();
});
