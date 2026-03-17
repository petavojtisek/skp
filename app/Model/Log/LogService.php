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

    public function getLogs(?int $limit = 10, ?int  $offset = 0) : array
    {
        return  $this->logDao->getLogs($limit, $offset);

    }


    public function addLog(LogEntity $log): int
    {

        if (!$log->getAdminId()) {
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

    public function logAction(string $module, string $action, string $name, $elementId = null, ?array $sendData = null, ?array $beforeData = null, ?string $codeName = null): void
    {
        /* TODO */
        /*
        $log = new LogEntity();
        $log->setVariable('module', $module);
        $log->setVariable('action', $action);
        $log->setVariable('name', $name);
        $log->setVariable('code_name', $codeName);
        $log->setVariable('element_id', $elementId ? (int)$elementId : null);

        if ($sendData) $log->setAfter($sendData);
        if ($beforeData) $log->setBefore($beforeData);

        $this->addLog($log);
        */
    }

}
