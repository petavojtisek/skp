<?php

namespace App\Model\Install;

use App\Model\Base\BaseDao;

class InstallDao extends BaseDao
{
    protected string $entityName = 'Install\\InstallEntity';

    /** @var InstallMapper */
    protected $mapper;

    public function __construct(InstallMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return InstallMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
