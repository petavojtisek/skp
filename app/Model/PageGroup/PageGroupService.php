<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseService;
use App\Model\System\ModelEventManager;

class PageGroupService extends BaseService
{
    private PageGroupDao $pageGroupDao;
    private ModelEventManager $eventManager;

    public function __construct(PageGroupDao $pageGroupDao, ModelEventManager $eventManager)
    {
        $this->pageGroupDao = $pageGroupDao;
        $this->eventManager = $eventManager;
    }

    public function findAll(): array
    {
        return $this->pageGroupDao->findAll() ?: [];
    }

    public function find(int $id): ?PageGroupEntity
    {
        return $this->pageGroupDao->find($id) ?: null;
    }

    public function save(PageGroupEntity $entity): int
    {
        return (int)$this->pageGroupDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->pageGroupDao->delete($id);
    }

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupDao->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
        $this->eventManager->trigger('rights_changed', $adminGroupId);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupDao->getAdminGroupIds($pageGroupId);
    }

    public function getAccessiblePageGroupIds(int $adminGroupId): array
    {
        return $this->pageGroupDao->getAccessiblePageGroupIds($adminGroupId);
    }

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->pageGroupDao->getAccessiblePageGroupNames($adminGroupId);
    }
}
