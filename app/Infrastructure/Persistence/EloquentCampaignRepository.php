<?php

namespace App\Infrastructure\Persistence;

use App\Models\Campaign as CampaignModel;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use Carbon\Carbon;

class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    public function save(Campaign $campaign): Campaign
    {
        $model = CampaignModel::create([
            'title' => $campaign->title,
            'description' => $campaign->description,
            'goal_amount' => $campaign->goalAmount,
            'creator_id' => $campaign->creatorId,
            'start_date' => $campaign->startDate,
            'end_date' => $campaign->endDate,
        ]);

        return new Campaign(
            title: $model->title,
            description: $model->description,
            goalAmount: $model->goal_amount,
            creatorId: $model->creator_id,
            startDate: Carbon::parse($model->start_date),
            endDate: Carbon::parse($model->end_date),
            id: $model->id
        );
    }

    public function findById(int $id): Campaign
    {
        $model = CampaignModel::findOrFail($id);
        return new Campaign(
            title: $model->title,
            description: $model->description,
            goalAmount: $model->goal_amount,
            creatorId: $model->creator_id,
            startDate: Carbon::parse($model->start_date),
            endDate: Carbon::parse($model->end_date),
            id: $model->id
        );
    }

    public function update(Campaign $campaign): Campaign
    {
        $model = CampaignModel::findOrFail($campaign->id);
        $model->update([
            'title' => $campaign->title,
            'description' => $campaign->description,
            'goal_amount' => $campaign->goalAmount,
        ]);
        return new Campaign(
            title: $model->title,
            description: $model->description,
            goalAmount: $model->goal_amount,
            creatorId: $model->creator_id,
            startDate: Carbon::parse($model->start_date),
            endDate: Carbon::parse($model->end_date),
            id: $model->id
        );
    }

    public function delete(int $id): void
    {
        CampaignModel::destroy($id);
    }

    /**
     * @return Campaign[]
     */
    public function all(): array
    {
        return CampaignModel::all()->map(function (CampaignModel $model): Campaign {
            return new Campaign(
                title: $model->title,
                description: $model->description,
                goalAmount: $model->goal_amount,
                creatorId: $model->creator_id,
                startDate: Carbon::parse($model->start_date),
                endDate: Carbon::parse($model->end_date),
                id: $model->id
            );
        })->all();
    }
}