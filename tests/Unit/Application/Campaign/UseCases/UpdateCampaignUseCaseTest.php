<?php

use App\Application\Campaign\UseCases\UpdateCampaignUseCase;
use App\Application\Campaign\DTOs\UpdateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Support\Carbon;

use function Pest\Laravel\mock;

it('updates a campaign when valid and not started', function () {
    $repo = mock(CampaignRepositoryInterface::class);
    $useCase = new UpdateCampaignUseCase($repo);

    $existing = new Campaign(
        id: 1,
        title: 'Old Title',
        description: 'Old Description',
        goalAmount: 1000,
        startDate: now()->addDays(2),
        endDate: now()->addDays(10),
        creator: new User(1, 'alice@acme.test', 'employee')
    );

    $dto = new UpdateCampaignDTO(
        id: 1,
        title: 'New Title',
        description: 'New Desc',
        goalAmount: 2000,
        actor: new User(1, 'alice@acme.test', 'employee'),
        startDate: now()->addDays(3),
        endDate: now()->addDays(12),
    );

    $repo->shouldReceive('findById')->once()->with(1)->andReturn($existing);
    $repo->shouldReceive('update')->once()->andReturn($existing);

    $updated = $useCase->execute($dto);

    expect($updated)->toBeInstanceOf(Campaign::class);
});

it('throws if campaign already started and dates are being changed', function () {
    $repo = mock(CampaignRepositoryInterface::class);
    $useCase = new UpdateCampaignUseCase($repo);

    $existing = new Campaign(
        id: 1,
        title: 'Running Campaign',
        description: 'Live now',
        goalAmount: 5000,
        startDate: now()->subDay(), // already started
        endDate: now()->addDays(5),
        creator: new User(2, 'owner@acme.test', 'employee')
    );

    $dto = new UpdateCampaignDTO(
        id: 1,
        title: 'New Title',
        description: '...',
        goalAmount: 7000,
        actor: new User(2, 'owner@acme.test', 'employee'),
        startDate: now()->addDays(1), // trying to modify
        endDate: now()->addDays(10),
    );

    $repo->shouldReceive('findById')->once()->with(1)->andReturn($existing);

    $useCase->execute($dto);
})->throws(DomainException::class, "Can't modify campaign dates once it has started.");
