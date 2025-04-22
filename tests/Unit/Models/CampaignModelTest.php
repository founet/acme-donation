<?php

use App\Models\Campaign;

/** @phpstan-template TValue */
it('returns correct status based on start and end dates', function () {
    $upcoming = Campaign::factory()->make([
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(5),
    ]);

    $active = Campaign::factory()->make([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(2),
    ]);

    $ended = Campaign::factory()->make([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDay(),
    ]);

    expect($upcoming->status)->toBe('upcoming');
    expect($active->status)->toBe('active');
    expect($ended->status)->toBe('ended');
});
