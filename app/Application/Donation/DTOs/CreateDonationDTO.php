<?php
namespace App\Application\Donation\DTOs;

use App\Domain\User\Entities\User;

class CreateDonationDTO
{
    public function __construct(
        public int $amount,
        public string $currency,
        public int $campaignId,
        public User $donor,
        public string $paymentSource
    ) {}
}