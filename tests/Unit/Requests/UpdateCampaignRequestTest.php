<?php

use App\Http\Requests\UpdateCampaignRequest;
use Illuminate\Support\Facades\Validator;

it('passes when updating title only', function () {
    $data = ['title' => 'New Title'];

    $request = new UpdateCampaignRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('passes with valid start and end date', function () {
    $data = [
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
