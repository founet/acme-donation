<?php

use App\Application\Campaign\UseCases\UpdateCampaignUseCase;
use App\Application\Campaign\DTOs\UpdateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;
use Mockery\Expectation;
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

    /** @var Expectation $expect0 **/
    $expect0 = $repository->shouldReceive('findById');
    $expect0->once()
        ->with($dto->id)
        ->andReturn($campaign);

    /** @var Expectation $expect1 **/
    $expect1 = $repository->shouldReceive('update');
    $expect1->once()
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
    /** @var Expectation $expectation **/
    $expectation = $repository->shouldReceive('findById');
    $expectation->once()->andReturn($existing);

    $useCase->execute($dto);
})->throws(DomainException::class, "Can't modify campaign dates once it has started.");