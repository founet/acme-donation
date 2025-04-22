<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['title', 'description', 'goal_amount', 'creator_id'];

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
