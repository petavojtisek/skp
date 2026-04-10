<?php

namespace App\Model\Module;

use App\Model\Base\BaseService;
use App\Model\ModuleGroupRight\ModuleGroupRightService;
use App\Model\ModulePermission\ModulePermissionService;
use App\Model\ModuleRight\ModuleRightService;

class ModuleService extends BaseService
{
    private ModuleDao $moduleDao;
    private ModuleGroupRightService $moduleGroupRightService;
    private ModulePermissionService $modulePermissionService;
    private ModuleRightService $moduleRightService;

    public function __construct(
        ModuleDao $moduleDao,
        ModuleGroupRightService $moduleGroupRightService,
        ModulePermissionService $modulePermissionService,
        ModuleRightService $moduleRightService
    ) {
        $this->moduleDao = $moduleDao;
        $this->moduleGroupRightService = $moduleGroupRightService;
        $this->modulePermissionService = $modulePermissionService;
        $this->moduleRightService = $moduleRightService;
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

    public function findActiveByType(int $type): array
    {
        return $this->moduleDao->findActiveByType($type);
    }

    public function getModuleByInstallId(int $installId): ?ModuleEntity
    {
        return $this->moduleDao->getModuleByInstallId($installId);
    }

    public function toggleActiveByInstallId(int $installId, bool $state): void
    {
        $module = $this->getModuleByInstallId($installId);
        if ($module) {
            $module->setModuleActive($state ? 'Y' : 'N');
            $this->moduleDao->save($module);
        }
    }

    /**
     * Sestaví matici oprávnění pro konkrétní modul a skupinu
     * @param int $moduleId
     * @param int $groupId
     * @return array
     */
    public function getModulePermissionsMatrix(int $moduleId, int $groupId): array
    {
        // 1. Aktivní práva pro danou skupinu a modul
        $activePermissions = $this->moduleGroupRightService->getPermissionsForGroupAndModule($groupId, $moduleId);

        // 2. Všechna definovaná práva pro tento modul
        $modulePermissionIds = $this->moduleRightService->getPermissionsByModule($moduleId);

        if (empty($modulePermissionIds)) {
            return [];
        }

        // 3. Detaily práv (názvy atd.)
        $permissions = $this->modulePermissionService->getPermissionsByIds($modulePermissionIds);

        $matrix = [];
        foreach ($permissions as $permId => $perm) {
            $matrix[$permId] = new \ArrayObject([
                'module_permission_id' => $permId,
                'name' => $perm->getName(),
                'is_active' => isset($activePermissions[$permId])
            ], \ArrayObject::ARRAY_AS_PROPS);
        }

        return $matrix;
    }

    public function togglePermission(int $moduleId, int $groupId, int $permissionId, bool $state): void
    {
        $this->moduleGroupRightService->togglePermission($moduleId, $groupId, $permissionId, $state);
    }

    /**
     * Vrací pole oprávnění pro danou skupinu s použitím existujících služeb
     * Formát: ['module_code' => ['permission_code' => 1, ...]]
     */
    public function getModuleRights(int $groupId): array
    {
        // 1. Získáme všechna přidělená práva pro skupinu
        $rawRights = $this->moduleGroupRightService->getPermissionsForGroup($groupId);
        if (empty($rawRights)) {
            return [];
        }

        // 2. Seskupíme si ID práv podle ID modulů
        $modulePermIds = [];
        $allPermIds = [];
        foreach ($rawRights as $row) {
            $modulePermIds[$row->module_id][] = $row->permission_id;
            $allPermIds[] = $row->permission_id;
        }

        // 3. Hromadně načteme detaily práv
        $permissions = $this->modulePermissionService->getPermissionsByIds(array_unique($allPermIds));

        $matrix = [];
        foreach ($modulePermIds as $moduleId => $pIds) {
            // Načteme entitu modulu pro získání code_name
            $module = $this->find($moduleId);
            if (!$module) continue;

            $moduleCode = $module->getModuleCodeName();
            $matrix[$moduleCode] = [];

            foreach ($pIds as $pId) {
                if (isset($permissions[$pId])) {
                    $rightCode = $permissions[$pId]->getRightCodeName();
                    $matrix[$moduleCode][$rightCode] = 1;
                }
            }
        }

        return $matrix;
    }
}
