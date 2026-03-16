<?php

namespace App\Model\AdminRight;

use App\Model\AdminGroupRight\AdminGroupRightDao;

class AdminRightFacade
{
    /** @var AdminRightDao */
    private $adminRightDao;

    /** @var AdminGroupRightDao */
    private $adminGroupRightDao;

    public function __construct(AdminRightDao $adminRightDao, AdminGroupRightDao $adminGroupRightDao)
    {
        $this->adminRightDao = $adminRightDao;
        $this->adminGroupRightDao = $adminGroupRightDao;
    }

    public function getAllRights(): array
    {
        return $this->adminRightDao->findAll();
    }

    public function getGroupRightsIds(int $groupId): array
    {
        return $this->adminGroupRightDao->getGroupRightsIds($groupId);
    }

    public function toggleGroupRight(int $groupId, int $rightId, bool $state): void
    {
        $this->adminGroupRightDao->toggleRight($groupId, $rightId, $state);
    }
}
