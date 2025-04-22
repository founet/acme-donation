<?php
namespace App\Domain\Campaign\Entities;


class Campaign
{
    public function __construct(
        public string $title,
        public string $description,
        public int $goalAmount,
        public int $creatorId,
        public \DateTimeInterface $startDate,
        public \DateTimeInterface $endDate,
        public ?int $id = null
    ) {}

    public function isStarted(): bool {
        return $this->startDate <= now()->startOfDay();
    }

    public function getStatus(): string {
        $today = now()->startOfDay();
        return match (true) {
            $today < $this->startDate => 'upcoming',
            $today > $this->endDate => 'ended',
            default => 'active',
        };
    }

    public function canReceiveDonations(): bool {
        return $this->getStatus() === 'active';
    }

}