<?php

namespace App\Model\AdminGroup;

class AdminGroupFacade
{
    private AdminGroupService $adminGroupService;

    public function __construct(AdminGroupService $adminGroupService)
    {
        $this->adminGroupService = $adminGroupService;
    }

    public function getGroups(): array
    {
        return $this->adminGroupService->findAll();
    }

    public function getGroup(int $id): ?AdminGroupEntity
    {
        return $this->adminGroupService->find($id);
    }

    public function saveGroup(AdminGroupEntity $entity): int
    {
        return $this->adminGroupService->save($entity);
    }

    public function deleteGroup(int $id): void
    {
        $this->adminGroupService->delete($id);
    }

    public function getGroupTree(int $startId = 0): array
    {
        return $this->adminGroupService->getGroupTree($startId);
    }

    public function getAdminGroups(): array
    {
        return $this->adminGroupService->getAdminGroups();
    }

    public function getAdminInGroups(int $adminId): array
    {
        return $this->adminGroupService->getAdminInGroups($adminId);
    }

    public function saveAdminGroups(int $adminId, array $groupIds): void
    {
        $this->adminGroupService->saveAdminGroups($adminId, $groupIds);
    }

    public function getAvailableGroups(int $startGroupId): array
    {
      
        if($startGroupId == 0) {
            throw new \InvalidArgumentException("Group with ID $startGroupId does not exist.");
        }
        return $this->adminGroupService->getAvailableGroups($startGroupId);
    }
}
