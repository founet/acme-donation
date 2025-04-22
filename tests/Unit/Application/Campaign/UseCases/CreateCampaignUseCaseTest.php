<?php

use App\Application\Campaign\UseCases\CreateCampaignUseCase;
use App\Application\Campaign\DTOs\CreateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\mock;

it('creates a campaign with valid data', function () {
    $repo = mock(CampaignRepositoryInterface::class);
    $useCase = new CreateCampaignUseCase($repo);

    $creator = new User(1, 'jane@acme.test', 'employee');

    $dto = new CreateCampaignDTO(
        title: 'Save the Trees',
        description: 'Reforest the Amazon',
        goalAmount: 10000,
        startDate: now()->addDay(),
        endDate: now()->addDays(10),
        creator: $creator
    );

    $repo->shouldReceive('create')
        ->once()
        ->withArgs(function (Campaign $campaign) use ($dto) {
            expect($campaign->title)->toBe($dto->title);
            expect($campaign->goalAmount)->toBe($dto->goalAmount);
            return true;
        })
        ->andReturnUsing(fn () => new Campaign(...get_object_vars($dto)));

    $result = $useCase->execute($dto);

    expect($result)->toBeInstanceOf(Campaign::class);
    expect($result->title)->toBe('Save the Trees');
});

it('throws if end date is before start date', function () {
    $useCase = new CreateCampaignUseCase(mock(CampaignRepositoryInterface::class));

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
