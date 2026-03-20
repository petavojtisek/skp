<?php

namespace App\Model\Install;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class InstallDao extends BaseDao
{
    protected string $entityName = 'Install\\InstallEntity';

    /** @var InstallMapper */
    protected IMapper $mapper;

    public function __construct(InstallMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return InstallMapper
     */

}
