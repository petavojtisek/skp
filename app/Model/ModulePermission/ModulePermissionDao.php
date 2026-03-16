<?php

namespace App\Model\ModulePermission;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModulePermissionDao extends BaseDao
{
    protected string $entityName = 'ModulePermission\\ModulePermissionEntity';

    /** @var ModulePermissionMapper */
    protected IMapper $mapper;

    public function __construct(ModulePermissionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
