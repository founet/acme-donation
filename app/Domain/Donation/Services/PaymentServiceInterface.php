<?php
namespace App\Domain\Donation\Services;

interface PaymentServiceInterface
{
    public function charge(int $amount, string $currency, string $source): PaymentResult;
}