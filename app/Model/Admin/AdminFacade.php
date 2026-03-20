<?php

namespace App\Model\Admin;

use App\Model\Log\LogFacade;
use App\Model\AdminGroup\AdminGroupService;
use App\Model\AdminGroupRight\AdminGroupRightService;
use App\Model\Module\ModuleService;
use App\Model\Presentation\PresentationService;
use App\Model\PageGroup\PageGroupService;
use App\Model\System\Cache;
use App\Model\System\ModelEventManager;


class AdminFacade
{
    private AdminService $adminService;
    private AdminGroupService $adminGroupService;
    private AdminGroupRightService $adminGroupRightService;
    private PresentationService $presentationService;
    private PageGroupService $pageGroupService;
    private ModuleService $moduleService;
    private LogFacade $logFacade;
    private Cache $cache;
    private ModelEventManager $eventManager;

    public function __construct(
        AdminService $adminService,
        AdminGroupService $adminGroupService,
        AdminGroupRightService $adminGroupRightService,
        PresentationService $presentationService,
        PageGroupService $pageGroupService,
        ModuleService $moduleService,
        LogFacade $logFacade,
        Cache $cache,
        ModelEventManager $eventManager
    ) {
        $this->adminService = $adminService;
        $this->adminGroupService = $adminGroupService;
        $this->adminGroupRightService = $adminGroupRightService;
        $this->presentationService = $presentationService;
        $this->pageGroupService = $pageGroupService;
        $this->moduleService = $moduleService;
        $this->logFacade = $logFacade;
        $this->cache = $cache;
        $this->eventManager = $eventManager;
    }

    public function getActiveAdmins(?array $groupIds = null): array
    {
        return $this->adminService->getActiveAdmins($groupIds);
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
}
