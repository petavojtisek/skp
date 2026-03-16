<?php

namespace App\Model\Admin;

use App\Model\Base\BaseService;
use Dibi\DateTime;

class AdminService extends BaseService
{
    /** @var AdminDao */
    private $adminDao;

    public function __construct(AdminDao $adminDao)
    {
        $this->adminDao = $adminDao;
    }

    public function getActiveAdmins(): array
    {
        return $this->adminDao->getActiveAdmins();
    }

    public function getAdmin(int $id): ?AdministratorEntity
    {
        return $this->adminDao->find($id) ?: null;
    }

    public function saveAdmin(AdministratorEntity $admin): int
    {
        return (int)$this->adminDao->save($admin)->getId();
    }

    public function softDelete(int $id): void
    {
        $admin = $this->getAdmin($id);
        if ($admin) {
            $admin->status = constant('C_ADMINISTRATOR_STATUS_REMOVED');
            $admin->disabled_dt = new DateTime();
            $this->adminDao->update($admin);
        }
    }

    public function getAdminGroups(): array { return $this->adminDao->getAdminGroups(); }
    public function getAdminInGroups(int $adminId): array { return $this->adminDao->getAdminInGroups($adminId); }
    public function saveAdminGroups(int $adminId, array $groupIds): void { $this->adminDao->saveAdminGroups($adminId, $groupIds); }

    public function getAdminPresentations(int $adminId): array { return $this->adminDao->getAdminPresentations($adminId); }
    public function saveAdminPresentations(int $adminId, array $presentationIds): void { $this->adminDao->saveAdminPresentations($adminId, $presentationIds); }

    public function loadLoggedUserEntity(int $adminId, LoggedUserEntity $entity): void
    {
        $admin = $this->getAdmin($adminId);
        if ($admin) {
            $entity->fillEntity($admin->getEntityData(), false);

            $groups = $this->getAdminGroups();
            $entity->setGroup(isset($groups[$admin->admin_group_id]) ? (array)$groups[$admin->admin_group_id] : null);

            $userPresIds = $this->getAdminPresentations($adminId);
            $presMap = [];
            foreach ($userPresIds as $pid) {
                $presMap[$pid] = 1;
            }
            $entity->setPresentations($presMap);
            $entity->setRights($this->getLoggedUserRights($entity));
        }
    }

    public function getLoggedUserRights(LoggedUserEntity $entity): array
    {

        return [
            'groups_right' => $this->getGroupsRight((int)$entity->getGroup()),
            'module_rights' => $this->getModuleRights((int)$entity->getId()),
            'page_rights' => $this->getPageRights((int)$entity->getGroup()),
        ];
    }

    private function getGroupsRight(int $groupId): array
    {
        if (!$groupId) return [];

        return $this->adminDao->getGroupsRight($groupId);
    }

    private function getModuleRights(int $adminId): array
    {
        return [];
    }

    private function getPageRights(int $groupId): array
    {
        if (!$groupId) return [];

        return $this->adminDao->getPageRights($groupId);
    }
}
