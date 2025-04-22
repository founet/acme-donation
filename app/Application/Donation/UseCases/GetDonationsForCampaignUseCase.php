<?php
namespace App\Application\Donation\UseCases;

namespace App\Application\Donation\UseCases;

use App\Domain\Donation\Repositories\DonationRepositoryInterface;

class GetDonationsForCampaignUseCase
{
    public function __construct(private DonationRepositoryInterface $repository) {}

    public function execute(int $campaignId): array
    {
        return $this->repository->findByCampaignId($campaignId);
    }
}