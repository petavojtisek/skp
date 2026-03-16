<?php

namespace App\Model\Log;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class LogDao extends BaseDao
{
    protected string $entityName = 'Log\\LogEntity';

    /** @var LogMapper */
    protected IMapper $mapper;

    public function __construct(LogMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getLogs(?int $limit = 10, ?int  $offset = 0) : array
    {
        $res = [];
        $logs = $this->mapper->getLogsWithAdmin($limit, $offset = 0);
        foreach ($logs as $log) {
            $res[] = $this->getEntity($this->entityName, (array)$log);
        }
        return $res;
    }

}
