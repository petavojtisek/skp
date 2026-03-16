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
}
