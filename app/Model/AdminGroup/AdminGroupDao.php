<?php

namespace App\Model\AdminGroup;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class AdminGroupDao extends BaseDao
{
    protected string $entityName = 'AdminGroup\\AdminGroupEntity';

    /** @var AdminGroupMapper */
    protected $mapper;

    public function __construct(AdminGroupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
