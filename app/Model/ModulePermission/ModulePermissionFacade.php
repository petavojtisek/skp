<?php

namespace App\Model\ModulePermission;

class ModulePermissionFacade
{
    /** @var ModulePermissionService */
    private $modulePermissionService;

    public function __construct(ModulePermissionService $modulePermissionService)
    {
        $this->modulePermissionService = $modulePermissionService;
    }

    public function getPermissions(): array
    {
        return $this->modulePermissionService->findAll();
    }

    public function getPermission(int $id): ?ModulePermissionEntity
    {
        return $this->modulePermissionService->find($id);
    }

    public function savePermission(ModulePermissionEntity $entity): int
    {
        return $this->modulePermissionService->save($entity);
    }

    public function deletePermission(int $id): void
    {
        $this->modulePermissionService->delete($id);
    }
}
