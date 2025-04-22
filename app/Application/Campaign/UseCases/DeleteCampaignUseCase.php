<?php
namespace App\Application\Campaign\UseCases;

use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User;

class DeleteCampaignUseCase
{
    public function __construct(private CampaignRepositoryInterface $repository) {}

    public function execute(int $id, User $actor): void
    {
        $campaign = $this->repository->findById($id);

        if ($campaign->creatorId !== $actor->id) {
            throw new \DomainException("You can't delete this campaign.");
        }

        $this->repository->delete($id);
    }
}
