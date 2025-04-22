<?php
namespace App\Application\Campaign\DTOs;
use App\Domain\User\Entities\User;

class UpdateCampaignDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public int $goalAmount,
        public \DateTimeInterface $startDate,
        public \DateTimeInterface $endDate,
        public User $editor
    ) {}
}