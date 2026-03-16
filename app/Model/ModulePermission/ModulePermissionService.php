<?php

namespace App\Model\ModulePermission;

use App\Model\Base\BaseService;

class ModulePermissionService extends BaseService
{
    /** @var ModulePermissionDao */
    private $modulePermissionDao;

    public function __construct(ModulePermissionDao $modulePermissionDao)
    {
        $this->modulePermissionDao = $modulePermissionDao;
    }

    public function findAll(): array
    {
        return $this->modulePermissionDao->findAll() ?: [];
    }

    public function find(int $id): ?ModulePermissionEntity
    {
        return $this->modulePermissionDao->find($id) ?: null;
    }

    public function save(ModulePermissionEntity $entity): int
    {
        return (int)$this->modulePermissionDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->modulePermissionDao->delete($id);
    }
}
