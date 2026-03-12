<?php

namespace App\Model\Log;

use App\Model\Base\BaseDao;

class LogDao extends BaseDao
{
    protected $entityName = 'Log\LogEntity';

    public function __construct(LogMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getLogs(): array
    {
        return $this->mapper->getLogsWithAdmin();
    }
}
