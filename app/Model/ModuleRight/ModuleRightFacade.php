<?php

namespace App\Model\ModuleRight;

class ModuleRightFacade
{
    private ModuleRightService $moduleRightService;

    public function __construct(ModuleRightService $moduleRightService)
    {
        $this->moduleRightService = $moduleRightService;
    }

    public function findAll(): array
    {
        return $this->moduleRightService->findAll();
    }

    public function find(int $id): ?ModuleRightEntity
    {
        return $this->moduleRightService->find($id);
    }

    public function save(ModuleRightEntity $entity): int
    {
        return $this->moduleRightService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->moduleRightService->delete($id);
    }
}
