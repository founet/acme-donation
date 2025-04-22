<?php
namespace App\Domain\Donation\Repositories;

use App\Domain\Donation\Entities\Donation;

interface DonationRepositoryInterface
{
    public function save(Donation $donation): Donation;
    /**
     * @return Donation[]
     */
    public function findByCampaignId(int $campaignId): array;
    /**
     * @return Donation[]
     */
    public function findByUserId(int $userId): array;


}