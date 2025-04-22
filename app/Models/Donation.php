<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @property int $id
 * @property int $employee_id
 * @property int $campaign_id
 * @property int $amount
 * @property string $currency
 * @property string $status
 */
class Donation extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount', 'currency', 'employee_id', 'campaign_id', 'status'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
