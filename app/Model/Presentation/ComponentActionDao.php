<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class ComponentActionDao extends BaseDao
{
    protected string $entityName = 'ComponentActionEntity';

    /** @var ComponentActionMapper */
    protected $mapper;

    public function __construct(ComponentActionMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return ComponentActionMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
