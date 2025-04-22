<?php

use App\Http\Requests\UpdateCampaignRequest;
use Illuminate\Support\Facades\Validator;

it('passes when required fields are provided including title', function () {
    $data = [
        'title' => 'Updated Title',
        'description' => 'Updated description text.',
        'goal_amount' => 500,
    ];

    $request = new UpdateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('passes with valid start and end date', function () {
    $data = [
        'title' => 'Water Project',
        'description' => 'Build clean water infrastructure for community',
        'goal_amount' => 1000,
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
    ];

    $request = new UpdateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('fails if end_date is before start_date', function () {
    $data = [
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(3)->toDateString(),
    ];

    $request = new UpdateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('end_date'))->toBeTrue();
});
