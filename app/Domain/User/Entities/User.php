<?php
namespace App\Domain\User\Entities;

class User
{
    public function __construct(
        public int $id,
        public string $email,
        public string $role = 'employee'
    ) {}

    public function canCreateCampaign(): bool
    {
        return in_array($this->role, ['employee', 'admin']);
    }

    public function canDonate(): bool
    {
        return $this->role !== 'suspended';
    }
}
