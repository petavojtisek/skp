<?php

namespace App\Model\Log;

class LogFacade
{
    /** @var LogService */
    private $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function getAllLogs(?int $limit = 10, ?int  $offset = 0): array
    {
        return $this->logService->getLogs($limit, $offset);
    }

}
