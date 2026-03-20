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

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->mapper->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->mapper->getAdminGroupIds($pageGroupId);
    }

    public function getAccessiblePageGroupIds(int $adminGroupId): array
    {
        // Poznámka: Tato metoda v mapperu chybí nebo má jiný název, 
        // ale v předchozích verzích tu byla. Pro jistotu ji vracím přes mapper.
        return $this->db->select('page_group_id, 1 as val')
            ->from('page_group_admin_group')
            ->where('admin_group_id = %i', $adminGroupId)
            ->fetchPairs('page_group_id', 'val');
    }

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

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->mapper->getAdminGroupIdsByPageGroups($pageGroupIds);
    }

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->mapper->togglePageInTable('page_in_group', $pageId, $pageGroupId, $state);
    }

    public function getPageGroupIdsByPageId(int $pageId): array
    {
        return $this->mapper->getPageGroupIdsFromTable('page_in_group', $pageId);
    }
}
