<?php

namespace App\Model\PageGroup;

class PageGroupFacade
{
    private PageGroupService $pageGroupService;

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

    public function togglePageGroup(int $pageId, int $pageGroupId, bool $state, string $type = 'user'): void
    {
        $this->pageGroupService->togglePageGroup($pageId, $pageGroupId, $state, $type);
    }

    public function getPageGroupIds(int $pageId, string $type = 'user'): array
    {
        return $this->pageGroupService->getPageGroupIds($pageId, $type);
    }

    // --- Metody pro PageGroupsPresenter (správa skupin jako takových) ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupService->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupService->getAdminGroupIds($pageGroupId);
    }

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->pageGroupService->getAdminGroupIdsByPageGroups($pageGroupIds);
    }

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->pageGroupService->getAccessiblePageGroupNames($adminGroupId);
    }

    // --- Zpětná kompatibilita ---

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->pageGroupService->togglePageGroup($pageId, $pageGroupId, $state, 'user');
    }

    public function getPageGroupIdsByPageId(int $pageId): array
    {
        return $this->pageGroupService->getPageGroupIds($pageId, 'user');
    }
}
