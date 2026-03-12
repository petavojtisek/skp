<?php

namespace App\Model\Admin;

use App\Model\Base\BaseDao;

class AdminDao extends BaseDao
{
    protected string $entityName = 'Admin\AdministratorEntity';

    /** @var AdminMapper */
    protected $mapper;

    public function __construct(AdminMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getActiveAdmins(): array
    {
        $data = $this->mapper->getActiveAdmins();
        return $this->getEntities($this->entityName, $data);
    }

    public function getAdminGroups(): array
    {
        return $this->mapper->getAdminGroups();
    }

    public function getAdminInGroups(int $adminId): array
    {
        return $this->mapper->getAdminInGroups($adminId);
    }

    public function saveAdminGroups(int $adminId, array $groupIds): void
    {
        $this->mapper->saveAdminGroups($adminId, $groupIds);
    }

    public function getAdminPresentations(int $adminId): array
    {
        return $this->mapper->getAdminPresentations($adminId);
    }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void
    {
        $this->mapper->saveAdminPresentations($adminId, $presentationIds);
    }

    /**
     * @return AdminMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
