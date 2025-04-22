<?php

namespace App\Events;

use App\Domain\Donation\Entities\Donation;

class DonationConfirmed
{
    /**
     * Create a new event instance.
     */
    public function __construct(public Donation $donation) {}
}
