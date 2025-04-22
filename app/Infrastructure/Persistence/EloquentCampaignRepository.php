<?php

namespace App\Infrastructure\Persistence;

use App\Models\Campaign as CampaignModel;
use App\Domain\Campaign\Entities\Campaign;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;

class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    public function save(Campaign $campaign): Campaign
    {
        $model = CampaignModel::create([
            'title' => $campaign->title,
            'description' => $campaign->description,
            'goal_amount' => $campaign->goalAmount,
            'creator_id' => $campaign->creatorId,
        ]);

        return new Campaign(
            $model->title,
            $model->description,
            $model->goal_amount,
            $model->creator_id,
            $model->id
        );
    }

    public function findById(int $id): Campaign
    {
        $model = CampaignModel::findOrFail($id);
        return new Campaign($model->title, $model->description, $model->goal_amount, $model->creator_id, $model->id);
    }

    public function update(Campaign $campaign): Campaign
    {
        $model = CampaignModel::findOrFail($campaign->id);
        $model->update([
            'title' => $campaign->title,
            'description' => $campaign->description,
            'goal_amount' => $campaign->goalAmount,
        ]);
        return new Campaign($model->title, $model->description, $model->goal_amount, $model->creator_id, $model->id);
    }

    public function delete(int $id): void
    {
        CampaignModel::destroy($id);
    }

    public function all(): array
    {
        return CampaignModel::all()->toArray();
    }
}