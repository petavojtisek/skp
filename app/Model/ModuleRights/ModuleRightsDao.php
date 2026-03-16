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
}
