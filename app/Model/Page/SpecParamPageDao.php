<?php

namespace App\Model\Page;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class SpecParamPageDao extends BaseDao
{
    protected string $entityName = 'Page\SpecParamPageEntity';

    /** @var SpecParamPageMapper */
    protected IMapper $mapper;

    public function __construct(SpecParamPageMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
