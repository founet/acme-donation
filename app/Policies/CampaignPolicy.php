<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewDonations(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->creator_id || $user->role === 'admin';
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->creator_id || $user->role === 'admin';
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->creator_id || $user->role === 'admin';
    }
}
