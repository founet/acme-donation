<?php

use App\Application\Campaign\UseCases\UpdateCampaignUseCase;
use App\Application\Campaign\DTOs\UpdateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;
use Mockery\MockInterface;

beforeEach(function () {

});

it('updates a campaign when valid and not started', function () {
    /** @var MockInterface&CampaignRepositoryInterface $repository */
    $repository = Mockery::mock(CampaignRepositoryInterface::class);
    $useCase = new UpdateCampaignUseCase($repository);
    $creator = new User(1, 'alice@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Old Title',
        description: 'Old Description',
        goalAmount: 1000,
        creatorId: $creator->id,
        startDate: now()->addDays(2),
        endDate: now()->addDays(10),
        id: 1,
    );
    $dto = new UpdateCampaignDTO(
        id: 1,
        title: 'Updated Title',
        description: 'Updated Desc',
        goalAmount: 1500,
        startDate: now()->addDay(),
        endDate: now()->addDays(10),
        editor: $creator,
    );

    $repository
        ->shouldReceive('findById')
        ->once()
        ->with($dto->id)
        ->andReturn($campaign);

    $repository
        ->shouldReceive('update')
        ->once()
        ->andReturnUsing(fn ($campaign) => $campaign);



    $updated = $useCase->execute($dto);

    expect($updated)->toBeInstanceOf(Campaign::class);
});

it('throws if campaign already started and dates are being changed', function () {
    /** @var MockInterface&CampaignRepositoryInterface $repository */
    $repository = Mockery::mock(CampaignRepositoryInterface::class);
    $useCase = new UpdateCampaignUseCase($repository);
    $creator = new User(2, 'owner@acme.test', 'employee');
    $existing = new Campaign(
        title: 'Running Campaign',
        description: 'Live now',
        goalAmount: 5000,
        creatorId: $creator->id,
        startDate: now()->subDay(), // already started
        endDate: now()->addDays(5),
        id: 1,
    );

    $dto = new UpdateCampaignDTO(
        id: 1,
        title: 'New Title',
        description: '...',
        goalAmount: 7000,
        startDate: now()->addDays(1),
        endDate: now()->addDays(10), // trying to modify
        editor: new User(2, 'owner@acme.test', 'employee'),
    );

    $repository->allows('findById')->once()->andReturn($existing);

    $useCase->execute($dto);
})->throws(DomainException::class, "Can't modify campaign dates once it has started.");