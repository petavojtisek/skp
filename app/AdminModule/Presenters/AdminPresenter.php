<?php

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use App\Model\Admin\AdminFacade;
use App\Model\Admin\LoggedUserEntity;
use App\Model\Presentation\PresentationFacade;

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
}
