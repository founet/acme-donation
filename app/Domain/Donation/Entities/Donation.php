<?php
namespace App\Domain\Donation\Entities;

class Donation
{
    public function __construct(
        public int $amount,
        public string $currency,
        public int $employeeId,
        public int $campaignId,
        public string $status = 'pending',
        public ?int $id = null
    ) {}
}