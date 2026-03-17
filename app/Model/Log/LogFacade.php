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

    public function logAction(string $module, string $action, string $name, $elementId = null, ?array $sendData = null, ?array $beforeData = null, ?string $codeName = null): void
    {
        $this->logService->logAction($module, $action, $name, $elementId, $sendData, $beforeData, $codeName);
    }

}
