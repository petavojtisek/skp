<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class PageGroupDao extends BaseDao
{
    protected string $entityName = 'PageGroup\PageGroupEntity';

    /** @var PageGroupMapper */
    protected IMapper $mapper;

    public function __construct(PageGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }



    // --- Skupina stránek <-> Administrátorská skupina ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->mapper->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->mapper->getAdminGroupIds($pageGroupId);
    }

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->mapper->getAdminGroupIdsByPageGroups($pageGroupIds);
    }

    // --- Stránka <-> Skupina stránek (Administrace) ---

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->mapper->togglePageInGroup($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupIds(int $pageId): array
    {
        return $this->mapper->getPageInGroupIds($pageId);
    }

    // --- Stránka <-> Uživatelská skupina (Frontend) ---

    public function togglePageInGroupUser(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->mapper->togglePageInGroupUser($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupUserIds(int $pageId): array
    {
        return $this->mapper->getPageInGroupUserIds($pageId);
    }

    // --- Pomocné metody ---

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->mapper->getAccessiblePageGroupNames($adminGroupId);
    }

    public function getAccessiblePageGroupIdsWithNames(int $adminGroupId): array
    {
        return $this->mapper->getAccessiblePageGroupIdsWithNames($adminGroupId);
    }

    public function getPageGroupsByPageId(int $pageId): array
    {
        $data = $this->mapper->getPageGroupsByPageId($pageId);
        return $this->getEntities($this->entityName, $data) ?: [];
    }
}
