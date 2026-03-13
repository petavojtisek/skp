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

}
