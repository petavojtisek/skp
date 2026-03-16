<?php

namespace App\Model\ModuleGroupRight;

class ModuleGroupRightFacade
{
    private ModuleGroupRightService $moduleGroupRightService;

    public function __construct(ModuleGroupRightService $moduleGroupRightService)
    {
        $this->moduleGroupRightService = $moduleGroupRightService;
    }

    public function findAll(): array
    {
        return $this->moduleGroupRightService->findAll();
    }

    public function find(int $id): ?ModuleGroupRightEntity
    {
        return $this->moduleGroupRightService->find($id);
    }

    public function save(ModuleGroupRightEntity $entity): int
    {
        return $this->moduleGroupRightService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->moduleGroupRightService->delete($id);
    }
}
