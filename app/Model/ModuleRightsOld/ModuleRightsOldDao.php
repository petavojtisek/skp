<?php

namespace App\Model\ModuleRightsOld;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModuleRightsOldDao extends BaseDao
{
    protected string $entityName = 'ModuleRightsOld\ModuleRightsOldEntity';

    /** @var ModuleRightsOldMapper */
    protected IMapper $mapper;

    public function __construct(ModuleRightsOldMapper $mapper)
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
