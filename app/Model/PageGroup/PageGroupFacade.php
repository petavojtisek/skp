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

    // --- Skupina stránek <-> Administrátorská skupina ---

    public function toggleAdminGroup(int $pageGroupId, int $adminGroupId, bool $state): void
    {
        $this->pageGroupService->toggleAdminGroup($pageGroupId, $adminGroupId, $state);
    }

    public function getAdminGroupIds(int $pageGroupId): array
    {
        return $this->pageGroupService->getAdminGroupIds($pageGroupId);
    }

    // --- Stránka <-> Skupina stránek (Administrace) ---

    public function togglePageInGroup(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->pageGroupService->togglePageInGroup($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupIds(int $pageId): array
    {
        return $this->pageGroupService->getPageInGroupIds($pageId);
    }

    // --- Stránka <-> Uživatelská skupina (Frontend) ---

    public function togglePageInGroupUser(int $pageId, int $pageGroupId, bool $state): void
    {
        $this->pageGroupService->togglePageInGroupUser($pageId, $pageGroupId, $state);
    }

    public function getPageInGroupUserIds(int $pageId): array
    {
        return $this->pageGroupService->getPageInGroupUserIds($pageId);
    }

    // --- Pomocné metody ---

    public function getAdminGroupIdsByPageGroups(array $pageGroupIds): array
    {
        return $this->pageGroupService->getAdminGroupIdsByPageGroups($pageGroupIds);
    }

    public function getAccessiblePageGroupNames(int $adminGroupId): array
    {
        return $this->pageGroupService->getAccessiblePageGroupNames($adminGroupId);
    }

    public function getAccessiblePageGroupIdsWithNames(int $adminGroupId): array
    {
        return $this->pageGroupService->getAccessiblePageGroupIdsWithNames($adminGroupId);
    }

    public function getPageGroupsByPageId(int $pageId): array
    {
        return $this->pageGroupService->getPageGroupsByPageId($pageId);
    }
}
