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

    public function getLogs(?int $limit = 10, ?int $offset = 0, ?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null) : array
    {
        $res = [];
        $logs = $this->mapper->getLogsWithAdmin($limit, $offset, $search, $module, $dateFrom, $dateTo);
        foreach ($logs as $log) {
            $res[] = $this->getEntity($this->entityName, (array)$log);
        }
        return $res;
    }

    public function countLogs(?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        return $this->mapper->countLogs($search, $module, $dateFrom, $dateTo);
    }

    public function getUniqueModules(): array
    {
        return $this->mapper->getUniqueModules();
    }

}
