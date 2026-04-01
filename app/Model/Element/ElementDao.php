<?php

namespace App\Model\Element;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ElementDao extends BaseDao
{
    protected string $entityName = 'Element\\ElementEntity';

    /** @var ElementMapper */
    protected IMapper $mapper;

    public function __construct(ElementMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
