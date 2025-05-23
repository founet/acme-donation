<?php
namespace App\Infrastructure\Persistence;

use App\Models\Campaign;
use App\Models\Donation as DonationModel;
use App\Domain\Donation\Entities\Donation;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @param int $userId
     * @return Donation[]
     */
    public function findByUserId(int $userId): array
    {
        return DonationModel::where('employee_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * @param int $campaignId
     * @return Donation[]
     */
    public function findByCampaignId(int $campaignId): array
    {
        return DonationModel::where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}