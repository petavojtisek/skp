<?php

namespace App\Model\AdminGroupRight;

use App\Model\Base\BaseService;
use App\Model\System\ModelEventManager;

class AdminGroupRightService extends BaseService
{
    private AdminGroupRightDao $adminGroupRightDao;
    private ModelEventManager $eventManager;

    public function __construct(AdminGroupRightDao $adminGroupRightDao, ModelEventManager $eventManager)
    {
        $this->adminGroupRightDao = $adminGroupRightDao;
        $this->eventManager = $eventManager;
    }

    public function toggleRight(int $groupId, int $rightId, bool $state): void
    {
        $this->adminGroupRightDao->toggleRight($groupId, $rightId, $state);
        $this->eventManager->trigger('rights_changed', $groupId);
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
