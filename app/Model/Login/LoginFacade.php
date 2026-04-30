<?php

namespace App\Model\Login;

use App\Model\Admin\AdminService;
use App\Model\Admin\LoggedUserEntity;
use App\Model\AdminGroup\AdminGroupService;
use App\Model\AdminGroupRight\AdminGroupRightService;
use App\Model\Log\LogService;
use App\Model\Module\ModuleService;
use App\Model\PageGroup\PageGroupService;
use App\Model\Presentation\PresentationService;
use App\Model\System\Cache;
use Nette\Security\SimpleIdentity;
use Nette\Security\User;

class LoginFacade
{
    private LoginService $loginService;
    private AdminService $adminService;
    private AdminGroupService $adminGroupService;
    private AdminGroupRightService $adminGroupRightService;
    private PresentationService $presentationService;
    private PageGroupService $pageGroupService;
    private ModuleService $moduleService;
    private LogService $logService;
    private Cache $cache;

    /** @var User */
    public User $user;

    public function __construct(
        User $user,
        LoginService $loginService,
        LogService $logService,
        AdminService $adminService,
        AdminGroupService $adminGroupService,
        AdminGroupRightService $adminGroupRightService,
        PresentationService $presentationService,
        PageGroupService $pageGroupService,
        ModuleService $moduleService,
        Cache $cache
    ) {
        $this->user = $user;
        $this->loginService = $loginService;
        $this->logService = $logService;
        $this->adminService = $adminService;
        $this->adminGroupService = $adminGroupService;
        $this->adminGroupRightService = $adminGroupRightService;
        $this->presentationService = $presentationService;
        $this->pageGroupService = $pageGroupService;
        $this->moduleService = $moduleService;
        $this->cache = $cache;
    }

    public function setStorage(string $storage): void
    {
        $this->loginService->setStorage($storage);
    }

    public function login(string $username, string $password): void
    {
        if ($this->loginService->getStorage() === 'admin') {
            try {
                $credentials = $this->loginService->getCredential($username, $password);
                $identity = $this->user->getAuthenticator()->authenticate(['admin', $credentials]);

                if ($identity instanceof SimpleIdentity) {
                    // 1. Prepare enriched data
                    $entity = new LoggedUserEntity();
                    $this->loadLoggedUserEntity((int)$identity->getId(), $entity);

                    // 2. Create new identity with full data
                    $enrichedIdentity = new SimpleIdentity($identity->getId(), $identity->getRoles(), $entity->exportData());

                    // 3. Login with enriched identity
                    $this->user->login($enrichedIdentity);
                } else {
                    $this->user->login($identity);
                }

                $this->logService->logAction(
                    'System',
                    'LOGIN',
                    'Přihlášení uživatele: ' . $username,
                    (int)$this->user->getId()
                );
            } catch (\Nette\Security\AuthenticationException $e) {
                throw new \Nette\Security\AuthenticationException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /**
     * Loads complete data for the user into the LoggedUserEntity
     */
    public function loadLoggedUserEntity(int $adminId, LoggedUserEntity $entity): void
    {
        $this->adminService->loadLoggedUserEntity($adminId, $entity);

        if($entity->getId() > 0){
            $groupId = (int)$entity->getAdminGroupId();

            $group = $this->adminGroupService->find($groupId);
            $entity->setGroup($group);

            $userPresIds = $this->presentationService->getAdminPresentations($adminId);
            $presMap = [];
            foreach ($userPresIds as $pid) {
                $presMap[$pid] = 1;
            }
            $entity->setPresentations($presMap);
            $entity->setRights($this->getLoggedUserRights($entity));
        }
    }

    protected function getLoggedUserRights(LoggedUserEntity $entity): array
    {
        $groupId = (int)$entity->getAdminGroupId();

        return [
            'groups_right' => $this->adminGroupRightService->getGroupRightsCodes($groupId),
            'module_rights' => $this->moduleService->getModuleRights($groupId),
            'page_rights' => $this->pageGroupService->getAccessiblePageGroupIdsWithNames($groupId),
        ];
    }

    public function autoLoginByAdminId(int $adminId): void
    {
        $entity = new LoggedUserEntity();
        $this->loadLoggedUserEntity($adminId, $entity);

        if ($entity->getId() > 0) {
            $identity = new SimpleIdentity($adminId, ['admin'], $entity->exportData());
            $this->user->login($identity);

            $this->logService->logAction(
                'System',
                'AUTOLOGIN',
                'Automatické přihlášení uživatele ID: ' . $adminId,
                $adminId
            );
        }
    }
}
