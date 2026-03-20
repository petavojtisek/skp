<?php

namespace App\Model\Object;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ObjectDao extends BaseDao
{
    protected string $entityName = 'Object\\ObjectEntity';

    /** @var ObjectMapper */
    protected IMapper $mapper;

    public function __construct(ObjectMapper $mapper)
    {
        $this->mapper = $mapper;
    }


}
