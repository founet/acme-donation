<?php

namespace App\Http\Controllers;

use App\Application\Donation\UseCases\GetMyDonationsUseCase;
use App\Domain\User\Entities\User as DomainUser;
use App\Application\Donation\UseCases\CreateDonationUseCase;
use App\Application\Donation\DTOs\CreateDonationDTO;
use App\Http\Requests\CreateDonationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class DonationController extends Controller
{
    public function __construct(private CreateDonationUseCase $useCase)
    {
    }

    public function myDonations(GetMyDonationsUseCase $useCase, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user == null) {
            return ApiResponse::error('Unauthorized', null, 401);
        }
        $donations = $useCase->execute($user->id);

        return ApiResponse::success($donations, 'List of my donations.');
    }

    public function store(CreateDonationRequest $request): JsonResponse
    {
        try {
            /** @var User $auth */
            $auth = Auth::user();
            if ($auth == null) {
                return ApiResponse::error('Unauthorized', null, 401);
            }
            $donor = new DomainUser($auth->id, $auth->email, $auth->role);

            $dto = new CreateDonationDTO(
                $request->input('amount'),
                $request->input('currency'),
                $request->input('campaign_id'),
                $donor,
                $request->input('payment_source')
            );

            $donation = $this->useCase->execute($dto);

            return ApiResponse::success($donation, 'Donation confirmed.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}