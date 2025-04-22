<?php

use App\Domain\Campaign\Entities\Campaign;
use App\Domain\User\Entities\User;

it('returns status as upcoming if start date is in the future', function () {
    $creator = new User(1, 'user@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Test',
        description: '...',
        goalAmount: 1000,
        creatorId: $creator->id,
        startDate: now()->addDays(2),
        endDate: now()->addDays(10),
        id: 1,
    );

    expect($campaign->getStatus())->toBe('upcoming');
    expect($campaign->canReceiveDonations())->toBeFalse();
});

it('returns status as active if today is between start and end dates', function () {
    $creator = new User(1, 'user@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Active One',
        description: '...',
        goalAmount: 5000,
        creatorId: $creator->id,
        startDate: now()->subDay(),
        endDate: now()->addDays(5),
        id: 2,
    );

    expect($campaign->getStatus())->toBe('active');
    expect($campaign->canReceiveDonations())->toBeTrue();
});

it('returns status as ended if end date is before today', function () {
    $creator = new User(1, 'user@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Finished',
        description: '...',
        goalAmount: 8000,
        creatorId: $creator->id,
        startDate: now()->subDays(10),
        endDate: now()->subDay(),
        id: 3,
    );
    expect($campaign->getStatus())->toBe('ended');
    expect($campaign->canReceiveDonations())->toBeFalse();
});

it('detects isStarted() and isEnded()', function () {
    $creator = new User(1, 'user@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Range Check',
        description: '...',
        goalAmount: 1000,
        creatorId: $creator->id,
        startDate: now()->subDays(5),
        endDate: now()->subDay(),
        id: 4,
    );

    expect($campaign->isStarted())->toBeTrue();
    expect($campaign->isEnded())->toBeTrue();
});

it('detects no change when dates are the same', function () {
    $start = now();
    $end = now()->addDays(10);

    $campaign = new Campaign(
        id: 1,
        title: 'Test',
        description: 'Description',
        goalAmount: 1000,
        creatorId: 1,
        startDate: $start,
        endDate: $end,
    );

    expect($campaign->isTryingToChangeDates($start, $end))->toBeFalse();
});

it('detects a change when start date differs', function () {
    $campaign = new Campaign(
        id: 1,
        title: 'Test',
        description: 'Description',
        goalAmount: 1000,
        creatorId: 1,
        startDate: now(),
        endDate: now()->addDays(10),
    );

    $newStart = now()->addDay(); // changed
    $sameEnd = now()->addDays(10);

    expect($campaign->isTryingToChangeDates($newStart, $sameEnd))->toBeTrue();
});

it('detects a change when end date differs', function () {
    $campaign = new Campaign(
        id: 1,
        title: 'Test',
        description: 'Description',
        goalAmount: 1000,
        creatorId: 1,
        startDate: now(),
        endDate: now()->addDays(10),
    );

    $sameStart = now();
    $newEnd = now()->addDays(11); // changed

    expect($campaign->isTryingToChangeDates($sameStart, $newEnd))->toBeTrue();
});

it('detects a change when both dates differ', function () {
    $campaign = new Campaign(
        id: 1,
        title: 'Test',
        description: 'Description',
        goalAmount: 1000,
        creatorId: 1,
        startDate: now(),
        endDate: now()->addDays(10),
    );

    $newStart = now()->addDays(1);
    $newEnd = now()->addDays(11);

    expect($campaign->isTryingToChangeDates($newStart, $newEnd))->toBeTrue();
});
