<?php
namespace App\Domain\Donation\Services;
class PaymentResult {
    public function __construct(
        public bool $success,
        public ?string $transactionId = null
    ) {}
}
