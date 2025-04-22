<?php
namespace App\Application\Campaign\UseCases;

use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Application\Campaign\DTOs\UpdateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;

class UpdateCampaignUseCase
{
    public function __construct(private CampaignRepositoryInterface $repository) {}

    public function execute(UpdateCampaignDTO $dto): Campaign
    {
        $campaign = $this->repository->findById($dto->id);

        if ($campaign->isStarted() && ($dto->startDate || $dto->endDate)) {
            throw new \DomainException("Can't modify campaign dates once it has started.");
        }

        if ($campaign->creatorId !== $dto->editor->id) {
            throw new \DomainException("You can't edit this campaign.");
        }

        $updatedCampaign = new Campaign(
            $dto->title,
            $dto->description,
            $dto->goalAmount,
            $dto->editor->id,
            $dto->startDate,
            $dto->endDate,
            $dto->id
        );

        return $this->repository->update($updatedCampaign);
    }
}