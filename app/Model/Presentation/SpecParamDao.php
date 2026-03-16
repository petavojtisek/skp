<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class SpecParamDao extends BaseDao
{
    protected string $entityName = 'Presentation\\SpecParamEntity';

    /** @var SpecParamMapper */
    protected IMapper $mapper;

    public function __construct(SpecParamMapper $mapper)
    {
        $this->mapper = $mapper;
    }


}
