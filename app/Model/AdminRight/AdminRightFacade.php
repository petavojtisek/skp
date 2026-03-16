<?php

namespace App\Model\AdminRight;

use App\Model\AdminGroupRight\AdminGroupRightService;

class AdminRightFacade
{
    private AdminRightService $adminRightService;

    private AdminGroupRightService $adminGroupRightService;

    public function __construct(AdminRightService $adminRightService, AdminGroupRightService $adminGroupRightService)
    {
        $this->adminRightService = $adminRightService;
        $this->adminGroupRightService = $adminGroupRightService;
    }

    public function getAllRights(): array
    {
        return $this->adminRightService->findAll();
    }

    public function getGroupRightsIds(int $groupId): array
    {
        return $this->adminGroupRightService->getGroupRightsIds($groupId);
    }

    public function toggleGroupRight(int $groupId, int $rightId, bool $state): void
    {
        $this->adminGroupRightService->toggleRight($groupId, $rightId, $state);
    }
}
