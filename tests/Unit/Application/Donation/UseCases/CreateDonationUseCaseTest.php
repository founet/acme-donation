<?php

use App\Application\Donation\DTOs\CreateDonationDTO;
use App\Application\Donation\UseCases\CreateDonationUseCase;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->donationRepo = mock(DonationRepositoryInterface::class);
    $this->campaignRepo = mock(CampaignRepositoryInterface::class);
    $this->useCase = new CreateDonationUseCase($this->donationRepo, $this->campaignRepo);
});

it('creates a donation for an active campaign', function () {
    $employee = new User(1, 'donor@acme.test', 'employee');
    $campaign = new Campaign(
        id: 1,
        title: 'Clean Water',
        description: 'Build wells',
        goalAmount: 2000,
        startDate: now()->subDay(),
        endDate: now()->addDay(),
        creator: new User(2, 'owner@acme.test', 'employee')
    );

    $dto = new CreateDonationDTO(
        amount: 100,
        currency: 'EUR',
        campaignId: $campaign->id,
        donor: $employee
    );

    $this->campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with($campaign->id)
        ->andReturn($campaign);

    $this->donationRepo
        ->shouldReceive('create')
        ->once()
        ->andReturn(new Donation(...get_object_vars($dto)));

    $result = $this->useCase->execute($dto);

    expect($result)->toBeInstanceOf(Donation::class);
    expect($result->amount)->toBe(100);
});

it('throws if campaign has not started', function () {
    $dto = new CreateDonationDTO(
        amount: 50,
        currency: 'USD',
        campaignId: 2,
        donor: new User(1, 'early@acme.test', 'employee')
    );

    $futureCampaign = new Campaign(
        id: 2,
        title: 'Future Aid',
        description: 'Starts next week',
        goalAmount: 5000,
        startDate: now()->addDays(3),
        endDate: now()->addDays(10),
        creator: new User(5, 'someone@acme.test', 'employee')
    );

    $this->campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with(2)
        ->andReturn($futureCampaign);

    $this->useCase->execute($dto);
})->throws(DomainException::class, 'You can only donate to active campaigns.');

it('throws if campaign is already ended', function () {
    $dto = new CreateDonationDTO(
        amount: 75,
        currency: 'USD',
        campaignId: 3,
        donor: new User(1, 'late@acme.test', 'employee')
    );

    $endedCampaign = new Campaign(
        id: 3,
        title: 'Finished Project',
        description: 'Already done',
        goalAmount: 10000,
        startDate: now()->subDays(10),
        endDate: now()->subDay(),
        creator: new User(6, 'admin@acme.test', 'employee')
    );

    $this->campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with(3)
        ->andReturn($endedCampaign);

    $this->useCase->execute($dto);
})->throws(DomainException::class, 'You can only donate to active campaigns.');
