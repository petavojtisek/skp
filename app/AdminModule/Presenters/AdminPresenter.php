<?php

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use App\Model\Admin\AdminFacade;
use App\Model\Admin\LoggedUserEntity;
use App\Model\Presentation\PresentationFacade;
use App\Model\AdminGroup\AdminGroupFacade;

abstract class AdminPresenter extends BasePresenter
{
    /** @var int|null */
    protected $adminId;

    /** @var LoggedUserEntity @inject */
    public $loggedUserEntity;

    /** @var AdminFacade @inject */
    public $adminFacade;

    /** @var PresentationFacade @inject */
    public $presentationFacade;

    /** @var AdminGroupFacade @inject */
    public $groupFacade;

    public function startup(): void
    {
        parent::startup();
        
        if (!$this->getUser()->isLoggedIn() && !$this->isPresenter('Sign')) {
            $this->redirect('Sign:in');
        }

        if ($this->getUser()->isLoggedIn()) {
            $this->adminId = (int) $this->getUser()->getId();
            
            // Populate the entity via facade
            $this->adminFacade->loadLoggedUserEntity($this->adminId, $this->loggedUserEntity);

            // Handle active presentation via session
            $session = $this->getSession('admin_context');
            $activeId = $session->active_presentation_id;

            $userPresIds = array_keys($this->loggedUserEntity->presentations);
            
            if (!$activeId || !in_array($activeId, $userPresIds)) {
                $activeId = !empty($userPresIds) ? $userPresIds[0] : null;
                $session->active_presentation_id = $activeId;
            }

            $this->loggedUserEntity->active_presentation_id = $activeId ? (int)$activeId : null;

            // Pass list of available presentations to template for switcher
            if (!empty($userPresIds)) {
                $availablePres = [];
                foreach ($userPresIds as $pid) {
                    $p = $this->presentationFacade->getPresentation($pid);
                    if ($p) $availablePres[$pid] = $p;
                }
                $this->template->availablePresentations = $availablePres;
            } else {
                $this->template->availablePresentations = [];
            }

            $this->template->adminId = $this->adminId;
            $this->template->loggedUserEntity = $this->loggedUserEntity;
        }
    }

    /**
     * Signal to switch active presentation
     */
    public function handleSwitchPresentation($id): void
    {
        $id = (int)$id;
        $userPresIds = array_keys($this->loggedUserEntity->presentations);
        if (in_array($id, $userPresIds)) {
            $session = $this->getSession('admin_context');
            $session->active_presentation_id = $id;
            $this->flashMessage('Prezentace byla přepnuta.');
        }
        $this->redirect('this');
    }

    public function isPresenter(string $presenter): bool
    {
        return $this->getName() === 'Admin:' . $presenter;
    }

    public function isAllowedGroup(int $id): bool
    {
        return $this->canEditGroup($id);
    }

    public function canEditGroup(int $id): bool
    {
        if ($id === 0) return (int)$this->loggedUserEntity->getAdminGroupId() === 1;
        $allowedGroups = $this->groupFacade->getAvailableGroups((int)$this->loggedUserEntity->getAdminGroupId());
        return isset($allowedGroups[$id]);
    }

    public function canDeleteGroup(int $id): bool
    {
        $myGroupId = (int)$this->loggedUserEntity->getAdminGroupId();
        if ($id === $myGroupId) return false; // Cannot delete self
        
        $allowedGroups = $this->groupFacade->getAvailableGroups($myGroupId);
        return isset($allowedGroups[$id]);
    }

    public function canAddChildTo(int $parentId): bool
    {
        $myGroupId = (int)$this->loggedUserEntity->getAdminGroupId();
        $userGroup = $this->groupFacade->getGroup($myGroupId);
        $myParentId = $userGroup ? $userGroup->pid : 0;

        // I can add child to my group, my descendants, OR my immediate parent (to create siblings)
        if ($parentId === $myParentId) return true;
        
        $allowedGroups = $this->groupFacade->getAvailableGroups($myGroupId);
        return isset($allowedGroups[$parentId]);
    }

    public function isAllowedAdmin(int $adminId): bool
    {
        $admin = $this->adminFacade->getAdmin($adminId);
        if (!$admin) return false;
        return $this->canEditGroup((int)$admin->getAdminGroupId());
    }
}
