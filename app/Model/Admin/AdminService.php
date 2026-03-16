<?php

namespace App\Model\Admin;

use App\Model\Base\BaseService;
use Dibi\DateTime;

class AdminService extends BaseService
{
    private AdminDao $adminDao;

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

    public function loadLoggedUserEntity(int $adminId, LoggedUserEntity $entity): void
    {
        $admin = $this->getAdmin($adminId);
        if ($admin) {
            $entity->fillEntity($admin->getEntityData(), false);
        }
    }
}
