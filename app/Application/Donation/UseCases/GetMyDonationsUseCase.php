<?php
namespace App\Application\Donation\UseCases;

namespace App\Application\Donation\UseCases;

use App\Domain\Donation\Repositories\DonationRepositoryInterface;

class GetMyDonationsUseCase
{
    public function __construct(private DonationRepositoryInterface $repository) {}

    public function execute(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }
}
