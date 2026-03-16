<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseService;

class AdminGroupRightService extends BaseService
{
    private AdminGroupRightDao $adminGroupRightDao;

    public function __construct(AdminGroupRightDao $adminGroupRightDao)
    {
        $this->adminGroupRightDao = $adminGroupRightDao;
    }

    public function toggleRight(int $groupId, int $rightId, bool $state): void
    {
        $this->adminGroupRightDao->toggleRight($groupId, $rightId, $state);
    }

    public function getGroupRightsIds(int $groupId): array
    {
        return $this->adminGroupRightDao->getGroupRightsIds($groupId);
    }

    public function getGroupRightsCodes(int $groupId): array
    {
        return $this->adminGroupRightDao->getGroupRightsCodes($groupId);
    }
}
