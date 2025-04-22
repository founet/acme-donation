<?php

use App\Http\Requests\CreateCampaignRequest;
use Illuminate\Support\Facades\Validator;

it('passes when valid payload is provided', function () {
    $data = [
        'title' => 'Clean Oceans',
        'description' => 'Big mission',
        'goal_amount' => 5000,
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDays(3)->toDateString(),
    ];

    $request = new CreateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('fails if end_date is before start_date', function () {
    $data = [
        'title' => 'Bad Dates',
        'description' => 'Test',
        'goal_amount' => 500,
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
    ];

    $request = new CreateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('end_date'))->toBeTrue();
});
