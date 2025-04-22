<?php
namespace App\Application\Donation\UseCases;

namespace App\Application\Donation\UseCases;

use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;

class GetDonationsForCampaignUseCase
{
    public function __construct(private DonationRepositoryInterface $repository) {}

    /**
     * @return Donation[]
     */
    public function execute(int $campaignId): array
    {
        return $this->repository->findByCampaignId($campaignId);
    }
}