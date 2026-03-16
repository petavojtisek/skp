<?php

namespace App\AdminModule\Presenters;

use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\Module\ModuleFacade;
use App\Model\Install\InstallFacade;
use Nette\Application\UI\Form;

final class ModuleRightsPresenter extends AdminPresenter
{
    /** @inject */
    public AdminGroupFacade $groupFacade;

    /** @inject */
    public ModuleFacade $moduleFacade;

    /** @inject */
    public InstallFacade $installFacade;

    /** @persistent */
    public ?int $id = null; // module_id

    /** @persistent */
    public ?int $groupId = null; // admin_group_id

    public function actionDefault(): void
    {
        $this->id = null;
        $this->groupId = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Práva modulů';
        $this->template->modules = $this->moduleFacade->getModulesForModuleRightsList();
        $userAvailableGroups =  $this->groupFacade->getAvailableGroups((int)$this->loggedUserEntity->getGroupId());
        $groupItems = [];
        if(!empty($userAvailableGroups)){
            foreach ($userAvailableGroups as $g) {
                $groupItems[$g->getId()] = $g->getGroupName();
            }
        }
        $this->template->userAvailableGroups  = $groupItems;

    }

    public function renderEdit(int $id, ?int $groupId = null): void
    {
        $this->id = $id;
        if ($groupId) {
            $this->groupId = $groupId;
        }


        /*
        // Get module details
        $installed = $this->installFacade->getInstalledModules();
        $module = null;
        foreach ($installed as $inst) {
            $m = $this->installFacade->getModuleByInstallId($inst->id);
            if ($m && $m['module_id'] == $id) {
                $module = $m;
                break;
            }
        }

        if (!$module) {
            $this->error('Modul nebyl nalezen.');
        }

        $this->template->title = 'Editace práv modulu: ' . $module['module_name'];
        $this->template->module = $module;

        // Hierarchical group selection for the current user
        $availableGroups = $this->groupFacade->getAvailableGroups((int)$this->loggedUserEntity->getGroupId());
        $groupItems = [];
        foreach ($availableGroups as $g) {
            $groupItems[$g->admin_group_id] = $g->admin_group_name;
        }
        $this['groupForm']['groupId']->setItems($groupItems);

        if ($this->groupId) {
            $this['groupForm']->setDefaults(['groupId' => $this->groupId]);
            $this->template->permissions = $this->moduleRightsFacade->getModulePermissions($this->id, $this->groupId);
        } else {
            $this->template->permissions = [];
        }
        */
    }

    /**
     * AJAX signal to toggle module permission for a group
     */
    public function handleTogglePermission(int $permissionId, bool $state): void
    {
        if (!$this->id || !$this->groupId) {
            $this->error('Parametry modulu nebo skupiny chybí.');
        }

       // $this->moduleRightsFacade->toggleModuleGroupRight($this->groupId, $this->id, $permissionId, $state);

        if ($this->isAjax()) {
            $this->flashMessage('Právo bylo aktualizováno.');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    protected function createComponentGroupForm(): Form
    {
        $form = new Form;
        $form->addSelect('groupId', 'Administrátorská skupina')
            ->setPrompt('-- Vyberte skupinu --')
            ->setRequired('Vyberte skupinu pro editaci práv');

        $form->addSubmit('send', 'Načíst práva')
            ->setHtmlAttribute('class', 'btn btn-primary btn-sm');

        $form->onSuccess[] = [$this, 'groupFormSucceeded'];
        return $form;
    }

    public function groupFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->redirect('this', ['groupId' => $values->groupId]);
    }
}
