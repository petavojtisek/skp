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

    /**
     * @param string $type 'user' (page_in_group) nebo 'admin' (page_in_group_user)
     */
    public function togglePageGroup(int $pageId, int $pageGroupId, bool $state, string $type = 'user'): void
    {
        $table = ($type === 'admin') ? 'page_in_group_user' : 'page_in_group';
        $this->pageGroupDao->getMapper()->togglePageInTable($table, $pageId, $pageGroupId, $state);
    }

    /**
     * @param string $type 'user' nebo 'admin'
     */
    public function getPageGroupIds(int $pageId, string $type = 'user'): array
    {
        $table = ($type === 'admin') ? 'page_in_group_user' : 'page_in_group';
        return $this->pageGroupDao->getMapper()->getPageGroupIdsFromTable($table, $pageId);
    }

    // --- Metody pro PageGroupsPresenter (správa skupin jako takových) ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupDao->getMapper()->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
        $this->eventManager->trigger('rights_changed', $adminGroupId);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupDao->getMapper()->getAdminGroupIds($pageGroupId);
    }

    // Ponecháme původní pro kompatibilitu
    public function getPageGroupsByPageId(int $pageId): array
    {
        return $this->pageGroupDao->getPageGroupsByPageId($pageId);
    }

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->pageGroupDao->getAdminGroupIdsByPageGroups($pageGroupIds);
    }

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->pageGroupDao->getAccessiblePageGroupNames($adminGroupId);
    }
}
