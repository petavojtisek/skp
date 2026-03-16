<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseService;

class PageGroupService extends BaseService
{
    private PageGroupDao $pageGroupDao;

    public function __construct(PageGroupDao $pageGroupDao)
    {
        $this->pageGroupDao = $pageGroupDao;
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
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupDao->getAdminGroupIds($pageGroupId);
    }
}
