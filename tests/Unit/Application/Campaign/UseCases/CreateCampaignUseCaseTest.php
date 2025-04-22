<?php

use App\Application\Campaign\UseCases\CreateCampaignUseCase;
use App\Application\Campaign\DTOs\CreateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;
use Mockery\Expectation;
use Mockery\MockInterface;


it('creates a campaign with valid data', function () {
    /** @var MockInterface&CampaignRepositoryInterface $repository */
    $repository = Mockery::mock(CampaignRepositoryInterface::class);
    $useCase = new CreateCampaignUseCase($repository);

    $creator = new User(1, 'jane@acme.test', 'employee');
    $dto = new CreateCampaignDTO(
        title: 'Save the Trees',
        description: 'Reforest the Amazon',
        goalAmount: 10000,
        startDate: now()->addDay(),
        endDate: now()->addDays(10),
        creator: $creator
    );

    /** @var Expectation $expectation **/
    $expectation = $repository->shouldReceive('save');
    $expectation->once()
        ->with(Mockery::type(Campaign::class))
        ->andReturnUsing(fn ($campaign) => $campaign);

    $result = $useCase->execute($dto);

    expect($result)->toBeInstanceOf(Campaign::class);
    expect($result->title)->toBe('Save the Trees');;
    expect($result->goalAmount)->toBe(10000);
});

it('throws if end date is before start date', function () {
    /** @var MockInterface&CampaignRepositoryInterface $repository */
    $repository = Mockery::mock(CampaignRepositoryInterface::class);
    $useCase = new CreateCampaignUseCase($repository);
    $dto = new CreateCampaignDTO(
        title: 'Bad Dates',
        description: 'Test',
        goalAmount: 5000,
        startDate: now()->addDays(2),
        endDate: now()->addDay(), // 👈 end before start
        creator: new User(1, 'bob@acme.test', 'employee')
    );

    $useCase->execute($dto);
})->throws(DomainException::class, 'End date must be after start date.');
