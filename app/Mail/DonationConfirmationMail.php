<?php
namespace App\Mail;

    use App\Domain\Donation\Entities\Donation;
    use Illuminate\Mail\Mailable;

class DonationConfirmationMail extends Mailable
{
    public function __construct(public Donation $donation) {}

    public function build(): self
    {
        return $this->subject('Your Donation Confirmation')
            ->markdown('emails.donation.confirmation');
    }
}
