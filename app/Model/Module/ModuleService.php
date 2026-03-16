<?php

namespace App\Model\Module;

use App\Model\Base\BaseService;

class ModuleService extends BaseService
{
    private ModuleDao $moduleDao;

    public function __construct(ModuleDao $moduleDao)
    {
        $this->moduleDao = $moduleDao;
    }

    public function findAll(): array
    {
        return $this->moduleDao->findAll();
    }

    public function find(int $id): ?ModuleEntity
    {
        return $this->moduleDao->find($id);
    }

    public function save(ModuleEntity $entity): int
    {
        return (int)$this->moduleDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->moduleDao->delete($id);
    }

    public function getModuleByInstallId(int $installId): ?ModuleEntity
    {
        return  $this->moduleDao->getModuleByInstallId($installId);
    }


    public function getModuleRights(int $adminId)
    {
        return [];
    }
}
