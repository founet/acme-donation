<?php
namespace App\Infrastructure\Payment;

use App\Domain\Donation\Services\PaymentServiceInterface;
use App\Domain\Donation\Services\PaymentResult;

class FakePaymentService implements PaymentServiceInterface
{
    public function charge(int $amount, string $currency, string $source): PaymentResult
    {
        return new PaymentResult(true, 'txn_fake_' . uniqid());
    }
}