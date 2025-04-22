<?php

namespace App\Http\Controllers;

use App\Application\Campaign\DTOs\UpdateCampaignDTO;
use App\Application\Campaign\UseCases\CreateCampaignUseCase;
use App\Application\Campaign\DTOs\CreateCampaignDTO;
use App\Application\Campaign\UseCases\DeleteCampaignUseCase;
use App\Application\Campaign\UseCases\UpdateCampaignUseCase;
use App\Application\Donation\UseCases\GetDonationsForCampaignUseCase;
use App\Domain\Campaign\Repositories\CampaignRepositoryInterface;
use App\Domain\User\Entities\User as DomainUser;
use App\Helpers\ApiResponse;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function __construct(private CreateCampaignUseCase $useCase) {}

    public function store(CreateCampaignRequest $request)
    {
        try {
            $user = Auth::user();
            $creator = new DomainUser($user->id, $user->email, $user->role);

            $dto = new CreateCampaignDTO(
                $request->input('title'),
                $request->input('description'),
                $request->input('goal_amount'),
                $creator
            );

            $campaign = $this->useCase->execute($dto);

            return ApiResponse::success($campaign, 'Campaign created successfully.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 500);
        }
    }

    public function update(UpdateCampaignRequest $request, int $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $this->authorize('update', $campaign);

            $user = Auth::user();
            $actor = new DomainUser($user->id, $user->email, $user->role);

            $dto = new UpdateCampaignDTO(
                $id,
                $request->input('title'),
                $request->input('description'),
                $request->input('goal_amount'),
                $actor
            );

            $updated = app(UpdateCampaignUseCase::class)->execute($dto);
            return ApiResponse::success($updated, 'Campaign updated.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $this->authorize('delete', $campaign);

            $user = Auth::user();
            $actor = new DomainUser($user->id, $user->email, $user->role);

            app(DeleteCampaignUseCase::class)->execute($id, $actor);

            return ApiResponse::success(null, 'Campaign deleted.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function index()
    {
        return ApiResponse::success(app(CampaignRepositoryInterface::class)->all());
    }

    public function donations(int $id, GetDonationsForCampaignUseCase $useCase)
    {
        $user = Auth::user();
        $campaign = Campaign::findOrFail($id);

        $this->authorize('viewDonations', $campaign);

        $donations = $useCase->execute($id);

        return ApiResponse::success($donations, 'Donations for campaign.');
    }
}
