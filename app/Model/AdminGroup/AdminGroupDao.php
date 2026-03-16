<?php

namespace App\Model\AdminGroup;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class AdminGroupDao extends BaseDao
{
    protected string $entityName = 'AdminGroup\\AdminGroupEntity';

    /** @var AdminGroupMapper */
    protected IMapper $mapper;

    public function __construct(AdminGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
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
}
