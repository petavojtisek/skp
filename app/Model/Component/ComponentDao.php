<?php

namespace App\Model\Component;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ComponentDao extends BaseDao
{
    protected string $entityName = 'Component\ComponentEntity';

    /** @var ComponentMapper */
    protected IMapper $mapper;

    public function __construct(ComponentMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
