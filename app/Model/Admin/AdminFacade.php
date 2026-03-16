<?php

namespace App\Model\Admin;

use App\Model\Log\LogFacade;
use App\Model\AdminGroup\AdminGroupService;
use App\Model\AdminGroupRight\AdminGroupRightService;
use App\Model\Presentation\PresentationService;
use App\Model\PageGroup\PageGroupService;
use App\Model\ModuleRights\ModuleRightsService;

class AdminFacade
{
    private AdminService $adminService;
    private AdminGroupService $adminGroupService;
    private AdminGroupRightService $adminGroupRightService;
    private PresentationService $presentationService;
    private PageGroupService $pageGroupService;
    private ModuleRightsService $moduleRightsService;
    private LogFacade $logFacade;

    public function __construct(
        AdminService $adminService,
        AdminGroupService $adminGroupService,
        AdminGroupRightService $adminGroupRightService,
        PresentationService $presentationService,
        PageGroupService $pageGroupService,
        ModuleRightsService $moduleRightsService,
        LogFacade $logFacade
    ) {
        $this->adminService = $adminService;
        $this->adminGroupService = $adminGroupService;
        $this->adminGroupRightService = $adminGroupRightService;
        $this->presentationService = $presentationService;
        $this->pageGroupService = $pageGroupService;
        $this->moduleRightsService = $moduleRightsService;
        $this->logFacade = $logFacade;
    }

    public function getActiveAdmins(): array
    {
        return $this->adminService->getActiveAdmins();
    }

    public function getAdmin(int $id): ?AdministratorEntity
    {
        return $this->adminService->getAdmin($id);
    }

    public function saveAdmin(AdministratorEntity $admin): int
    {
        return $this->adminService->saveAdmin($admin);
    }

    public function softDelete(int $id): void
    {
        $this->adminService->softDelete($id);
    }

    public function getAdminGroups(): array { return $this->adminGroupService->getAdminGroups(); }

    public function getGroup(int $groupId): ?array {
        $groups = $this->getAdminGroups();
        return $groups[$groupId] ?? null;
    }

    public function getAdminInGroups(int $adminId): array { return $this->adminGroupService->getAdminInGroups($adminId); }

    public function saveAdminGroups(int $adminId, array $groupIds): void {
        $this->adminGroupService->saveAdminGroups($adminId, $groupIds);
    }

    public function getAdminPresentations(int $adminId): array { return $this->presentationService->getAdminPresentations($adminId); }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void {
        $this->presentationService->saveAdminPresentations($adminId, $presentationIds);
    }

    /**
     * Loads complete data for the logged-in user into the LoggedUserEntity
     */
    public function loadLoggedUserEntity(int $adminId, LoggedUserEntity $entity): void
    {
        $this->adminService->loadLoggedUserEntity($adminId, $entity);

        if($entity->getId() > 0){
            $groupId = (int)$entity->getGroupId();
            $groups = $this->getAdminGroups();
            $entity->setGroup(isset($groups[$groupId]) ? (array)$groups[$groupId] : null);

            $userPresIds = $this->getAdminPresentations($adminId);
            $presMap = [];
            foreach ($userPresIds as $pid) {
                $presMap[$pid] = 1;
            }
            $entity->setPresentations($presMap);
            $entity->setRights($this->getLoggedUserRights($entity));
            xdebug_break();
        }
    }

    protected function getLoggedUserRights(LoggedUserEntity $entity): array
    {
        $groupId = (int)$entity->getGroupId();
        $adminId = (int)$entity->getId();

        return [
            'groups_right' => $this->adminGroupRightService->getGroupRightsIds($groupId),
            'module_rights' => $this->moduleRightsService->getModuleRights($adminId),
            'page_rights' => $this->pageGroupService->getAdminGroupIds($groupId),
        ];
    }
}
