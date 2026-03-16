<?php

namespace App\Model\ModuleGroupRight;

use App\Model\Base\BaseService;

class ModuleGroupRightService extends BaseService
{
    private ModuleGroupRightDao $moduleGroupRightDao;

    public function __construct(ModuleGroupRightDao $moduleGroupRightDao)
    {
        $this->moduleGroupRightDao = $moduleGroupRightDao;
    }

    public function findAll(): array
    {
        return $this->moduleGroupRightDao->findAll();
    }

    public function find(int $id): ?ModuleGroupRightEntity
    {
        return $this->moduleGroupRightDao->find($id);
    }

    public function save(ModuleGroupRightEntity $entity): int
    {
        return (int)$this->moduleGroupRightDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->moduleGroupRightDao->delete($id);
    }
}
