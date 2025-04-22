<?php
namespace App\Infrastructure\Persistence;

use App\Models\Donation as DonationModel;
use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;

class EloquentDonationRepository implements DonationRepositoryInterface
{
    public function save(Donation $donation): Donation
    {
        $model = DonationModel::create([
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'employee_id' => $donation->employeeId,
            'campaign_id' => $donation->campaignId,
            'status' => $donation->status,
        ]);

        return new Donation(
            $model->amount,
            $model->currency,
            $model->employee_id,
            $model->campaign_id,
            $model->status,
            $model->id
        );
    }
    public function findByUserId(int $userId): array
    {
        return DonationModel::where('employee_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function findByCampaignId(int $campaignId): array
    {
        return DonationModel::with('campaign')
            ->where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'employee_id' => $donation->employee_id,
                    'status' => $donation->status,
                    'created_at' => $donation->created_at,
                    'campaign' => [
                        'id' => $donation->campaign->id,
                        'title' => $donation->campaign->title,
                        'goal_amount' => $donation->campaign->goal_amount,
                    ],
                ];
            })
            ->toArray();
    }

}