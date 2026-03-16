<?php

namespace App\Model\AdminRight;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class AdminRightDao extends BaseDao
{
    protected string $entityName = 'AdminRight\\AdminRightEntity';

    /** @var AdminRightMapper */
    protected $mapper;

    public function __construct(AdminRightMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }
}
