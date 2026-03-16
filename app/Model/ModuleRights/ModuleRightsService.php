<?php

namespace App\Model\ModuleRights;

use App\Model\Base\BaseService;

class ModuleRightsService extends BaseService
{
    private ModuleRightsDao $moduleRightsDao;

    public function __construct(ModuleRightsDao $moduleRightsDao)
    {
        $this->moduleRightsDao = $moduleRightsDao;
    }

    public function findAll(): array
    {
        return $this->moduleRightsDao->findAll() ?: [];
    }

    public function find(int $id): ?ModuleRightsEntity
    {
        return $this->moduleRightsDao->find($id) ?: null;
    }

    public function save(ModuleRightsEntity $entity): int
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
}
