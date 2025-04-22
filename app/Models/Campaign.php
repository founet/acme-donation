<?php

namespace App\Models;

use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $goal_amount
 * @property int $creator_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 *
 * @method static CampaignFactory factory(...$parameters)
 */
class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;
    protected $fillable = ['title', 'description', 'goal_amount', 'creator_id', 'start_date', 'end_date'];

    protected $appends = ['status'];

    public function getStatusAttribute(): string {
        $today = now()->startOfDay();
        return match (true) {
            $today < $this->start_date => 'upcoming',
            $today > $this->end_date => 'ended',
            default => 'active',
        };
    }
}
