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

    public function getPermissionsForGroupAndModule(int $groupId, int $moduleId): array
    {
        return $this->moduleGroupRightDao->getPermissionsForGroupAndModule($groupId, $moduleId);
    }

    public function togglePermission(int $moduleId, int $groupId, int $permissionId, bool $state): void
    {

        if ($state) {
            $entity = new ModuleGroupRightEntity([
                'module_id' => $moduleId,
                'admin_group_id' => $groupId,
                'permission_id' => $permissionId
            ]);
            $this->save($entity);
        } else {
            $this->moduleGroupRightDao->deleteBy($moduleId, $groupId, $permissionId);
        }
    }
}
