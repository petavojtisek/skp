<?php

namespace App\Model\ModuleGroupRight;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModuleGroupRightDao extends BaseDao
{
    protected string $entityName = 'ModuleGroupRight\\ModuleGroupRightEntity';

    /** @var ModuleGroupRightMapper */
    protected IMapper $mapper;

    public function __construct(ModuleGroupRightMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function getPermissionsForGroupAndModule(int $groupId, int $moduleId): array
    {
        return $this->mapper->getPermissionsForGroupAndModule($groupId, $moduleId);
    }

    public function deleteBy(int $moduleId, int $groupId, int $permissionId): void
    {
    
        $this->mapper->deleteBy(['module_id' => $moduleId, 'admin_group_id' => $groupId, 'permission_id' => $permissionId]);
    }
}
