<?php
namespace App\Application\Campaign\UseCases;

use App\Application\Campaign\DTOs\CreateCampaignDTO;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;

class CreateCampaignUseCase
{
    public function __construct(private CampaignRepositoryInterface $repository) {}

    public function execute(CreateCampaignDTO $dto): Campaign
    {
        if (!$dto->creator->canCreateCampaign()) {
            throw new \DomainException('Not authorized to create a campaign.');
        }
        if ($dto->startDate < now()) {
            throw new \DomainException('Start date must be today or later.');
        }

        if ($dto->endDate <= $dto->startDate) {
            throw new \DomainException('End date must be after start date.');
        }

        $campaign = new Campaign(
            $dto->title,
            $dto->description,
            $dto->goalAmount,
            $dto->creator->id,
            $dto->startDate,
            $dto->endDate,
        );

        return $this->repository->save($campaign);
    }
}
