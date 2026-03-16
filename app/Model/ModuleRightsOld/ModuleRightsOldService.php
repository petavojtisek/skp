<?php

namespace App\Model\ModuleRightsOld;

use App\Model\Base\BaseService;

class ModuleRightsOldService extends BaseService
{
    private ModuleRightsOldDao $moduleRightsDao;

    public function __construct(ModuleRightsOldDao $moduleRightsDao)
    {
        $this->moduleRightsDao = $moduleRightsDao;
    }

    public function findAll(): array
    {
        return $this->moduleRightsDao->findAll() ?: [];
    }

    public function find(int $id): ?ModuleRightsOldEntity
    {
        return $this->moduleRightsDao->find($id) ?: null;
    }

    public function save(ModuleRightsOldEntity $entity): int
    {
        return (int)$this->moduleRightsDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->moduleRightsDao->delete($id);
    }

    public function getModuleRights(int $adminId): array
    {
        return []; // To be implemented later with Install module logic
    }

    public function getModulePermissions(int $moduleId, int $adminGroupId): array
    {
        return $this->moduleRightsDao->getModulePermissions($moduleId, $adminGroupId);
    }

    public function toggleModuleGroupRight(int $adminGroupId, int $moduleId, int $permissionId, bool $state): void
    {
        $this->moduleRightsDao->toggleModuleGroupRight($adminGroupId, $moduleId, $permissionId, $state);
    }
}
