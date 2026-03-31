<?php

namespace App\Model\Log;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Base\BaseService;
use Dibi\DateTime;

class LogService extends BaseService
{
    /** @var LogDao */
    private $logDao;

    /** @var LoggedUserEntity */
    private $loggedUser;

    public function __construct(LogDao $logDao, LoggedUserEntity $loggedUser)
    {
        $this->logDao = $logDao;
        $this->loggedUser = $loggedUser;
    }

    public function getLogs(?int $limit = 10, ?int $offset = 0, ?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null) : array
    {
        return $this->logDao->getLogs($limit, $offset, $search, $module, $dateFrom, $dateTo);
    }

    public function countLogs(?string $search = null, ?string $module = null, ?string $dateFrom = null, ?string $dateTo = null): int
    {
        return $this->logDao->countLogs($search, $module, $dateFrom, $dateTo);
    }

    public function getUniqueModules(): array
    {
        return $this->logDao->getUniqueModules();
    }

    public function addLog(LogEntity $log): int
    {
        if (!$log->getAdminId()) {
            if ($this->loggedUser->getId()) {
                $log->setVariable('admin_id', (int)$this->loggedUser->getId());
            } else {
                $log->setVariable('admin_id', 0);
            }
        }

        return (int) $this->logDao->insert($log);
    }

    public function logAction(string $module, string $action, string $name, $elementId = null, ?array $sendData = null, ?array $beforeData = null, ?string $codeName = null): void
    {
        $log = new LogEntity();
        $log->setVariable('module', $module);
        $log->setVariable('action', $action);
        $log->setVariable('name', $name);
        $log->setVariable('code_name', $codeName);
        $log->setVariable('element_id', $elementId ? (int)$elementId : null);

        if ($sendData) $log->setAfter($sendData);
        if ($beforeData) $log->setBefore($beforeData);

        $this->addLog($log);
    }

}
