<?php

namespace App\Listeners;

use App\Events\DonationConfirmed;
use App\Mail\DonationConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendDonationConfirmationEmail
{
    public function handle(DonationConfirmed $event)
    {
        $donorEmail = DB::table('users')->where('id', $event->donation->employeeId)->value('email');

        if ($donorEmail) {
            Mail::to($donorEmail)->send(new DonationConfirmationMail($event->donation));
        }
    }
}