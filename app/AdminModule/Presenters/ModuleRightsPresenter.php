<?php

namespace App\AdminModule\Presenters;

use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\Module\ModuleFacade;
use App\Model\Install\InstallFacade;
use App\Model\Module\ModuleEntity;
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
    }

    public function renderEdit(int $id): void
    {
        $this->id = $id;

        /** @var ModuleEntity|null $module */
        $module = $this->moduleFacade->find($id);
        if (!$module) {
            $this->error('Modul nebyl nalezen.');
        }

        $this->template->title = 'Editace práv modulu: ' . $module->getModuleName();
        $this->template->module = $module;
        $this->template->moduleId = $this->id;
        $this->template->groupId = $this->groupId;

        $groups = $this->getAvailableGroupsAsOption();
        $this->template->userAvailableGroups = $groups;

        // Load permissions matrix via Facade
        $this->template->permissions = $this->groupId ? $this->moduleFacade->getModulePermissionsMatrix($this->id, $this->groupId) : [];

        $this['groupForm']['groupId']->setItems($groups);        if ($this->groupId) {
            $this['groupForm']->setDefaults(['groupId' => $this->groupId]);
        }
    }

    /**
     * Reusable method for getting available groups [id => name]
     */
    protected function getAvailableGroupsAsOption(): array
    {
        $userAvailableGroups = $this->groupFacade->getAvailableGroups((int)$this->loggedUserEntity->getGroupId());
        $groupItems = [];
        if (!empty($userAvailableGroups)) {
            foreach ($userAvailableGroups as $g) {
                $groupItems[$g->getId()] = $g->getGroupName();
            }
        }
        return $groupItems;
    }

    /**
     * AJAX signal to toggle module permission for a group
     */
    public function handleTogglePermission(int $id, int $groupId, int $permissionId, int $state): void
    {
        $this->id = $id;
        $this->groupId = $groupId;
        xdebug_break();
        $this->moduleFacade->togglePermission($id, $groupId, $permissionId, (bool)$state);

        if ($this->isAjax()) {
            $this->flashMessage('Právo bylo aktualizováno.');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    /**
     * AJAX signal for group selection change
     */
    public function handleSelectGroup(?int $groupId = null): void
    {
        $this->groupId = $groupId;
        if ($this->isAjax()) {
            $this->redrawControl('permissionsSnippet');
        } else {
            $this->redirect('this', ['groupId' => $groupId]);
        }
    }

    protected function createComponentGroupForm(): Form
    {
        $form = new Form;
        $form->addSelect('groupId', 'Administrátorská skupina')
            ->setPrompt('-- Vyberte skupinu --')
            ->setRequired('Vyberte skupinu pro editaci práv');

        $form->onSuccess[] = [$this, 'groupFormSucceeded'];
        return $form;
    }

    public function groupFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->groupId = $values->groupId;
        if ($this->isAjax()) {
            $this->redrawControl('permissionsSnippet');
        } else {
            $this->redirect('this', ['groupId' => $this->groupId]);
        }
    }

    /**
     * Mock permissions for the blind table
     */
    private function getMockPermissions(): array
    {
        return [
            (object)['module_permission_id' => 1, 'name' => 'Zobrazení', 'is_active' => true],
            (object)['module_permission_id' => 2, 'name' => 'Editace', 'is_active' => false],
            (object)['module_permission_id' => 3, 'name' => 'Mazání', 'is_active' => true],
        ];
    }
}
