<?php

namespace App\Model\ModuleRights;

class ModuleRightsFacade
{
    private ModuleRightsService $moduleRightsService;

    public function __construct(ModuleRightsService $moduleRightsService)
    {
        $this->moduleRightsService = $moduleRightsService;
    }

    public function getRights(): array
    {
        return $this->moduleRightsService->findAll();
    }

    public function getRight(int $id): ?ModuleRightsEntity
    {
        return $this->moduleRightsService->find($id);
    }

    public function saveRight(ModuleRightsEntity $entity): int
    {
        return $this->moduleRightsService->save($entity);
    }

    public function deleteRight(int $id): void
    {
        $this->moduleRightsService->delete($id);
    }
}
