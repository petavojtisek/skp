<?php

namespace App\Model\PageGroup;

class PageGroupFacade
{
    /** @var PageGroupService */
    private $pageGroupService;

    public function __construct(PageGroupService $pageGroupService)
    {
        $this->pageGroupService = $pageGroupService;
    }

    public function getPageGroups(): array
    {
        return $this->pageGroupService->findAll();
    }

    public function getPageGroup(int $id): ?PageGroupEntity
    {
        return $this->pageGroupService->find($id);
    }

    public function savePageGroup(PageGroupEntity $entity): int
    {
        return $this->pageGroupService->save($entity);
    }

    public function deletePageGroup(int $id): void
    {
        $this->pageGroupService->delete($id);
    }

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupService->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupService->getAdminGroupIds($pageGroupId);
    }
}
