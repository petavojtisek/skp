<?php

namespace App\Model\Module;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Install\InstallService;
use App\Model\AdminGroup\AdminGroupService;

class ModuleFacade
{
    private ModuleService $moduleService;
    private InstallService $installService;
    private AdminGroupService $adminGroupService;

    private LoggedUserEntity $loggedUser;

    public function __construct(
        ModuleService $moduleService,
        InstallService $installService,
        AdminGroupService $adminGroupService,
        LoggedUserEntity $loggedUser
    ) {
        $this->moduleService = $moduleService;
        $this->installService = $installService;
        $this->adminGroupService = $adminGroupService;
        $this->loggedUser = $loggedUser;
    }

    public function findAll(): array
    {
        return $this->moduleService->findAll();
    }

    public function find(int $id): ?ModuleEntity
    {
        return $this->moduleService->find($id);
    }

    public function save(ModuleEntity $entity): int
    {
        return $this->moduleService->save($entity);
    }

    public function delete(int $id): void
    {
        $this->moduleService->delete($id);
    }

    public function getModuleByInstallId(int $installId): ?ModuleEntity
    {
        return  $this->moduleService->getModuleByInstallId($installId);
    }

    public function getInstalledModules(): array
    {
        return $this->installService->getInstalledModules();
    }

    public function getModulePermissionsMatrix(int $moduleId, int $groupId): array
    {


        return $this->moduleService->getModulePermissionsMatrix($moduleId, $groupId);
    }

    public function togglePermission(int $moduleId, int $groupId, int $permissionId, bool $state): void
    {
        $this->moduleService->togglePermission($moduleId, $groupId, $permissionId, $state);
    }

    public function getAvailableGroups(int $startGroupId): array
    {
        return $this->adminGroupService->getAvailableGroups($startGroupId);
    }


    public function getModulesForModuleRightsList(): array
    {
        //nacteni instalovanych modulu
        $installed = $this->installService->getInstalledModules();
        $modules = [];
        //nacteni dat pro kazdy instalovany modul
        foreach ($installed as $install) {
            $moduleEntity = $this->getModuleByInstallId($install->getId());
            if ($moduleEntity) {
                $modules[$moduleEntity->getId()] = $moduleEntity;
            }
        }
        return $modules;
    }
}
