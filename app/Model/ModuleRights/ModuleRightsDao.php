<?php

namespace App\Model\ModuleRights;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModuleRightsDao extends BaseDao
{
    protected string $entityName = 'ModuleRights\ModuleRightsEntity';

    /** @var ModuleRightsMapper */
    protected IMapper $mapper;

    public function __construct(ModuleRightsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function getModulePermissions(int $moduleId, int $adminGroupId): array
    {
        return $this->mapper->getModulePermissions($moduleId, $adminGroupId);
    }

    public function toggleModuleGroupRight(int $adminGroupId, int $moduleId, int $permissionId, bool $state): void
    {
        $this->mapper->toggleModuleGroupRight($adminGroupId, $moduleId, $permissionId, $state);
    }
}
