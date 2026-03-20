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

    public function delete(int $id): void
    {
        $this->pageGroupDao->delete($id);
    }

    public function save(PageGroupEntity $entity): int
    {
        return (int)$this->pageGroupDao->save($entity)->getId();
    }

    // --- Skupina stránek <-> Administrátorská skupina ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupDao->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
        $this->eventManager->trigger('rights_changed', $adminGroupId);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupDao->getAdminGroupIds($pageGroupId);
    }

    // --- Stránka <-> Skupina stránek (Administrace) ---

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->pageGroupDao->togglePageInGroup($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupIds(int $pageId): array
    {
        return $this->pageGroupDao->getPageInGroupIds($pageId);
    }

    // --- Stránka <-> Uživatelská skupina (Frontend) ---

    public function togglePageInGroupUser(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->pageGroupDao->togglePageInGroupUser($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupUserIds(int $pageId): array
    {
        return $this->pageGroupDao->getPageInGroupUserIds($pageId);
    }

    // --- Pomocné metody ---

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->pageGroupDao->getAccessiblePageGroupNames($adminGroupId);
    }

    public function getAccessiblePageGroupIdsWithNames(int $adminGroupId): array
    {
        return $this->pageGroupDao->getAccessiblePageGroupIdsWithNames($adminGroupId);
    }

    public function getPageGroupsByPageId(int $pageId): array
    {
        return $this->pageGroupDao->getPageGroupsByPageId($pageId);
    }

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->pageGroupDao->getAdminGroupIdsByPageGroups($pageGroupIds);
    }
}
