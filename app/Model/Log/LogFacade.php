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

    public function getLogs(?int $limit = 10, ?int $offset = 0, ?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return $this->logService->getLogs($limit, $offset, $search, $module, $dateFrom, $dateTo);
    }

    public function countLogs(?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        return $this->logService->countLogs($search, $module, $dateFrom, $dateTo);
    }

    public function getUniqueModules(): array
    {
        return $this->logService->getUniqueModules();
    }

    public function logAction(string $module, string $action, string $name, $elementId = null, ?array $sendData = null, ?array $beforeData = null, ?string $codeName = null): void
    {
        $this->logService->logAction($module, $action, $name, $elementId, $sendData, $beforeData, $codeName);
    }

}
