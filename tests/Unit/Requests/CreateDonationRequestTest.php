<?php

use App\Http\Requests\CreateDonationRequest;
use Illuminate\Support\Facades\Validator;

it('passes with valid donation data', function () {
    $data = [
        'campaign_id' => 1,
        'amount' => 100,
        'currency' => 'EUR',
        'payment_source' => 'stripe'
    ];

    $request = new CreateDonationRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('fails when required fields are missing', function () {
    $data = []; // no fields at all

    $request = new CreateDonationRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('campaign_id'))->toBeTrue();
    expect($validator->errors()->has('amount'))->toBeTrue();
    expect($validator->errors()->has('currency'))->toBeTrue();
});

it('fails when currency is invalid format', function () {
    $data = [
        'campaign_id' => 1,
        'amount' => 50,
        'currency' => 'EURO' // ❌ should be 3 characters
    ];

    $request = new CreateDonationRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('currency'))->toBeTrue();
});
