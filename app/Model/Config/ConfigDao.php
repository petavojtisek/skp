<?php

namespace App\Model\Config;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ConfigDao extends BaseDao
{
    protected string $entityName = 'Config\\ConfigEntity';

    /** @var ConfigMapper */
    protected IMapper $mapper;

    public function __construct(ConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }





    /**
     * @return ConfigMapper
     */
    public function getMapper(): \App\Model\Base\IMapper
    {
        return $this->mapper;
    }
}
