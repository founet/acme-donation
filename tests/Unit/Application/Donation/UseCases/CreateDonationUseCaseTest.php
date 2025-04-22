<?php

use App\Application\Donation\DTOs\CreateDonationDTO;
use App\Application\Donation\UseCases\CreateDonationUseCase;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;
use App\Domain\Donation\Services\PaymentResult;
use App\Domain\Donation\Services\PaymentServiceInterface;
use App\Domain\User\Entities\User;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery\MockInterface;


beforeEach(function () {

});


it('creates a donation for an active campaign', closure: function () {
    /** @var MockInterface&DonationRepositoryInterface $donationRepo */
    $donationRepo = Mockery::mock(DonationRepositoryInterface::class);
    /** @var MockInterface&CampaignRepositoryInterface $campaignRepo */
    $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
    /** @var MockInterface&PaymentServiceInterface $paymentService */
    $paymentService = Mockery::mock(PaymentServiceInterface::class);
    /** @var MockInterface&Dispatcher $events */
    $events = Mockery::mock(Dispatcher::class);

    $useCase = new CreateDonationUseCase(
        $donationRepo,
        $campaignRepo,
        $paymentService,
        $events
    );
    $employee = new User(1, 'donor@acme.test', 'employee');
    $creator = new User(2, 'owner@acme.test', 'employee');
    $campaign = new Campaign(
        title: 'Clean Water',
        description: 'Build wells',
        goalAmount: 2000,
        creatorId: $creator->id,
        startDate: now()->subDay(),
        endDate: now()->addDay(),
        id: 1,
    );

    $dto = new CreateDonationDTO(
        amount: 100,
        currency: 'EUR',
        campaignId: (int)$campaign->id,
        donor: $employee,
        paymentSource: 'stripe'
    );

    $campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with($campaign->id)
        ->andReturn($campaign);

    $paymentService
        ->shouldReceive('charge')
        ->once()
        ->with($dto->amount, $dto->currency, $dto->paymentSource)
        ->andReturn(new PaymentResult(true));

    $events->shouldReceive('dispatch')->once()->andReturnNull();

    $donationRepo
        ->shouldReceive('save')
        ->once()
        ->andReturnUsing(fn ($donation) => $donation);

    $result = $useCase->execute($dto);

    expect($result)->toBeInstanceOf(Donation::class);
    expect($result->amount)->toBe(100);
});

it('throws if campaign has not started', function () {
    /** @var MockInterface&DonationRepositoryInterface $donationRepo */
    $donationRepo = Mockery::mock(DonationRepositoryInterface::class);
    /** @var MockInterface&CampaignRepositoryInterface $campaignRepo */
    $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
    /** @var MockInterface&PaymentServiceInterface $paymentService */
    $paymentService = Mockery::mock(PaymentServiceInterface::class);
    /** @var MockInterface&Dispatcher $events */
    $events = Mockery::mock(Dispatcher::class);

    $useCase = new CreateDonationUseCase(
        $donationRepo,
        $campaignRepo,
        $paymentService,
        $events
    );
    $dto = new CreateDonationDTO(
        amount: 50,
        currency: 'USD',
        campaignId: 2,
        donor: new User(1, 'early@acme.test', 'employee'),
        paymentSource: 'stripe'
    );
    $creator = new User(5, 'someone@acme.test', 'employee');
    $futureCampaign = new Campaign(
        title: 'Future Aid',
        description: 'Starts next week',
        goalAmount: 5000,
        creatorId: $creator->id,
        startDate: now()->addDays(3),
        endDate: now()->addDays(10),
        id: 2,
    );

    $campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with(2)
        ->andReturn($futureCampaign);

    $useCase->execute($dto);
})->throws(DomainException::class, 'You can only donate to active campaigns.');

it('throws if campaign is already ended', function () {
    /** @var MockInterface&DonationRepositoryInterface $donationRepo */
    $donationRepo = Mockery::mock(DonationRepositoryInterface::class);
    /** @var MockInterface&CampaignRepositoryInterface $campaignRepo */
    $campaignRepo = Mockery::mock(CampaignRepositoryInterface::class);
    /** @var MockInterface&PaymentServiceInterface $paymentService */
    $paymentService = Mockery::mock(PaymentServiceInterface::class);
    /** @var MockInterface&Dispatcher $events */
    $events = Mockery::mock(Dispatcher::class);

    $useCase = new CreateDonationUseCase(
        $donationRepo,
        $campaignRepo,
        $paymentService,
        $events
    );
    $dto = new CreateDonationDTO(
        amount: 75,
        currency: 'USD',
        campaignId: 3,
        donor: new User(1, 'late@acme.test', 'employee'),
        paymentSource: 'stripe'
    );

    $creator = new User(6, 'admin@acme.test', 'employee');
    $endedCampaign = new Campaign(
        title: 'Finished Project',
        description: 'Already done',
        goalAmount: 10000,
        creatorId: $creator->id,
        startDate: now()->subDays(10),
        endDate: now()->subDay(),
        id: 3,
    );

    $campaignRepo
        ->shouldReceive('findById')
        ->once()
        ->with(3)
        ->andReturn($endedCampaign);

    $useCase->execute($dto);
})->throws(DomainException::class, 'You can only donate to active campaigns.');
