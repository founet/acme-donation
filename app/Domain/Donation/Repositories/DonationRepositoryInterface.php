<?php
namespace App\Domain\Donation\Repositories;

use App\Domain\Donation\Entities\Donation;

interface DonationRepositoryInterface
{
    public function save(Donation $donation): Donation;
    public function findByCampaignId(int $campaignId): array;
    public function findByUserId(int $userId): array;


}