<?php

namespace App\Model\ModuleRight;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModuleRightDao extends BaseDao
{
    protected string $entityName = 'ModuleRight\\ModuleRightEntity';

    /** @var ModuleRightMapper */
    protected IMapper $mapper;

    public function __construct(ModuleRightMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function getPermissionsByModule(int $moduleId): array
    {
        return $this->mapper->getPermissionsByModule($moduleId);
    }
}
