<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseDao;

class SpecParamDao extends BaseDao
{
    protected string $entityName = 'Presentation\\SpecParamEntity';

    /** @var SpecParamMapper */
    protected $mapper;

    public function __construct(SpecParamMapper $mapper)
    {
        $this->mapper = $mapper;
    }


}
