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

    /** @var \Nette\Security\User */
    private $user;

    public function __construct(LogDao $logDao, LoggedUserEntity $loggedUser, \Nette\Security\User $user)
    {
        $this->logDao = $logDao;
        $this->loggedUser = $loggedUser;
        $this->user = $user;
    }

    public function getAllLogs(): array
    {
        return $this->logDao->getLogs();
    }

    public function addLog(LogEntity $log): int
    {
        if (!$log->inserted) {
            $log->setInserted(new DateTime());
        }
        
        if (!$log->admin_id) {
            if ($this->user->isLoggedIn()) {
                $log->setVariable('admin_id', (int)$this->user->getId());
            } elseif ($this->loggedUser->admin_id) {
                $log->setVariable('admin_id', (int)$this->loggedUser->admin_id);
            } else {
                $log->setVariable('admin_id', 0);
            }
        }
        
        return (int) $this->logDao->insert($log);
    }

    /**
     * Helper for quick logging
     */
    public function logAction(string $module, string $action, string $name, $elementId = null, ?array $sendData = null, ?array $beforeData = null, ?string $codeName = null): void
    {
        $log = new LogEntity();
        $log->setVariable('module', $module);
        $log->setVariable('action', $action);
        $log->setVariable('name', $name);
        $log->setVariable('code_name', $codeName);
        $log->setVariable('element_id', $elementId ? (int)$elementId : null);
        
        if ($sendData) $log->setSendData($sendData);
        if ($beforeData) $log->setBeforeData($beforeData);
        
        $this->addLog($log);
    }
}
