<?php

namespace App\Model\Admin;

use App\Model\Log\LogFacade;

class AdminFacade
{
    /** @var AdminService */
    private $adminService;

    /** @var LogFacade */
    private $logFacade;

    public function __construct(AdminService $adminService, LogFacade $logFacade)
    {
        $this->adminService = $adminService;
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

    public function getAdminGroups(): array { return $this->adminService->getAdminGroups(); }
    public function getGroup(int $groupId): ?array { 
        $groups = $this->getAdminGroups();
        return $groups[$groupId] ?? null;
    }
    public function getAdminInGroups(int $adminId): array { return $this->adminService->getAdminInGroups($adminId); }
    public function saveAdminGroups(int $adminId, array $groupIds): void { 
        $this->adminService->saveAdminGroups($adminId, $groupIds);
    }

    public function getAdminPresentations(int $adminId): array { return $this->adminService->getAdminPresentations($adminId); }
    public function saveAdminPresentations(int $adminId, array $presentationIds): void { 
        $this->adminService->saveAdminPresentations($adminId, $presentationIds);
    }

    /**
     * Loads complete data for the logged-in user into the LoggedUserEntity
     */
    public function loadLoggedUserEntity(int $adminId, LoggedUserEntity $entity): void
    {
        $this->adminService->loadLoggedUserEntity($adminId, $entity);
    }
}
