<?php

namespace App\Model\ModulePermission;

class ModulePermissionFacade
{
    private ModulePermissionService $modulePermissionService;

    public function __construct(ModulePermissionService $modulePermissionService)
    {
        $this->modulePermissionService = $modulePermissionService;
    }

    public function findAll(): array
    {
        return $this->modulePermissionService->findAll();
    }

    public function find(int $id): ?ModulePermissionEntity
    {
        return $this->modulePermissionService->find($id);
    }

    public function save(ModulePermissionEntity $entity): int
    {
        return $this->modulePermissionService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->modulePermissionService->delete($id);
    }
}
