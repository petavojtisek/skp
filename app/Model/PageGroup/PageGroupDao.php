<?php

namespace App\Model\PageGroup;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class PageGroupDao extends BaseDao
{
    protected string $entityName = 'PageGroup\PageGroupEntity';

    /** @var PageGroupMapper */
    protected $mapper;

    public function __construct(PageGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
