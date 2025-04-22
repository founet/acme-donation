<?php

namespace App\Providers;

use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\Donation\Repositories\DonationRepositoryInterface;
use App\Domain\Donation\Services\PaymentServiceInterface;
use App\Infrastructure\Payment\FakePaymentService;
use App\Infrastructure\Persistence\EloquentCampaignRepository;
use App\Infrastructure\Persistence\EloquentDonationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CampaignRepositoryInterface::class,
            EloquentCampaignRepository::class,
        );
        $this->app->bind(DonationRepositoryInterface::class, EloquentDonationRepository::class);
        $this->app->bind(PaymentServiceInterface::class, FakePaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
