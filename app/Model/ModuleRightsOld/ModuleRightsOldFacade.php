<?php

namespace App\Model\ModuleRightsOld;

class ModuleRightsOldFacade
{
    private ModuleRightsOldService $moduleRightsService;

    public function __construct(ModuleRightsOldService $moduleRightsService)
    {
        $this->moduleRightsService = $moduleRightsService;
    }

    public function getRights(): array
    {
        return $this->moduleRightsService->findAll();
    }

    public function getRight(int $id): ?ModuleRightsOldEntity
    {
        return $this->moduleRightsService->find($id);
    }

    public function saveRight(ModuleRightsOldEntity $entity): int
    {
        return $this->moduleRightsService->save($entity);
    }

    public function deleteRight(int $id): void
    {
        $this->moduleRightsService->delete($id);
    }

    public function getModulePermissions(int $moduleId, int $adminGroupId): array
    {
        return $this->moduleRightsService->getModulePermissions($moduleId, $adminGroupId);
    }

    public function toggleModuleGroupRight(int $adminGroupId, int $moduleId, int $permissionId, bool $state): void
    {
        $this->moduleRightsService->toggleModuleGroupRight($adminGroupId, $moduleId, $permissionId, $state);
    }
}
