<?php

namespace App\Domain\Campaign\Repositories;

use App\Domain\Campaign\Entities\Campaign;

interface CampaignRepositoryInterface
{
    public function save(Campaign $campaign): Campaign;
    public function findById(int $id): Campaign;
    public function update(Campaign $campaign): Campaign;
    public function delete(int $id): void;
    public function all(): array;
}