<?php

namespace App\Model\ModuleRight;

use App\Model\Base\BaseService;

class ModuleRightService extends BaseService
{
    private ModuleRightDao $moduleRightDao;

    public function __construct(ModuleRightDao $moduleRightDao)
    {
        $this->moduleRightDao = $moduleRightDao;
    }

    public function findAll(): array
    {
        return $this->moduleRightDao->findAll();
    }

    public function find(int $id): ?ModuleRightEntity
    {
        return $this->moduleRightDao->find($id);
    }

    public function save(ModuleRightEntity $entity): int
    {
        return (int)$this->moduleRightDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->moduleRightDao->delete($id);
    }

    public function getPermissionsByModule(int $moduleId): array
    {
        return $this->moduleRightDao->getPermissionsByModule($moduleId);
    }
}
