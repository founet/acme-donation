<?php
namespace App\Application\Donation\UseCases;

use App\Application\Donation\DTOs\CreateDonationDTO;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;
use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Services\PaymentServiceInterface;
use App\Events\DonationConfirmed;
use Illuminate\Contracts\Events\Dispatcher;

class CreateDonationUseCase
{
    public function __construct(
        private DonationRepositoryInterface $repository,
        private CampaignRepositoryInterface $campaignRepository,
        private PaymentServiceInterface $payment,
        private Dispatcher $events,
    ) {}

    public function execute(CreateDonationDTO $dto): Donation
    {
        if (!$dto->donor->canDonate()) {
            throw new \DomainException('Not allowed to donate.');
        }
        $campaign = $this->campaignRepository->findById($dto->campaignId);

        if (!$campaign->canReceiveDonations()) {
            throw new \DomainException("You can only donate to active campaigns.");
        }

        $result = $this->payment->charge(
            $dto->amount,
            $dto->currency,
            $dto->paymentSource
        );

        if (!$result->success) {
            throw new \RuntimeException('Payment failed.');
        }

        $donation = new Donation(
            $dto->amount,
            $dto->currency,
            $dto->donor->id,
            $dto->campaignId,
            'confirmed'
        );

        $donation =  $this->repository->save($donation);

        $this->events->dispatch(new DonationConfirmed($donation));

        return $donation;
    }
}