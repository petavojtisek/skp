<?php

namespace App\Model\Log;

use App\Model\Base\BaseDao;

class LogDao extends BaseDao
{
    protected string $entityName = 'Log\\LogEntity';

    /** @var LogMapper */
    protected $mapper;

    public function __construct(LogMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getLogs()
    {
        return [];
    }

}
