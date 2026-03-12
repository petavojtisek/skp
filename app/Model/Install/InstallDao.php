<?php

namespace App\Model\Install;

use App\Model\Base\BaseDao;

class InstallDao extends BaseDao
{
    protected $entityName = 'Install\InstallEntity';

    public function __construct(InstallMapper $mapper)
    {
        $this->mapper = $mapper;
    }
}
