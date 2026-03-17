<?php

namespace App\AdminModule\Presenters;

use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\AdminGroup\AdminGroupEntity;
use App\Model\AdminRight\AdminRightFacade;
use Nette\Application\UI\Form;

final class GroupsPresenter extends AdminPresenter
{
    /** @var AdminRightFacade @inject */
    public $rightFacade;

    /** @var int|null @persistent */
    public $id;

    public function actionDefault(): void
    {
        $this->id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Skupiny uživatelů';
        $this->template->tree = $this->groupFacade->getGroupTree((int)$this->loggedUserEntity->getAdminGroupId());
    }

    public function renderEdit(?int $id = null, ?int $parentId = null): void
    {
        if ($id === null && $this->id !== null) {
            $id = (int)$this->id;
        }

        if ($id && !$this->isAllowedGroup($id)) {
            $this->flashMessage('Nemáte oprávnění k editaci této skupiny.', 'error');
            $this->redirect('default');
        }

        $this->template->title = $id ? 'Editace skupiny' : 'Nová skupina';
        $this->template->id = $id;

        if ($id) {
            $group = $this->groupFacade->getGroup($id);
            if (!$group) {
                $this->error('Skupina nebyla nalezena');
            }
            $this['groupForm']->setDefaults($group->getEntityData());

            // Rights for the group
            $this->template->allRights = $this->rightFacade->getAllRights();
            $this->template->activeRightIds = $this->rightFacade->getGroupRightsIds($id);
        } elseif ($parentId) {
            if (!$this->isAllowedGroup($parentId)) {
                $this->flashMessage('Nemáte oprávnění vytvářet podskupinu v této skupině.', 'error');
                $this->redirect('default');
            }
            $this['groupForm']->setDefaults(['pid' => $parentId]);
            $this->template->allRights = [];
            $this->template->activeRightIds = [];
        } else {
            $this->template->allRights = [];
            $this->template->activeRightIds = [];
        }
    }

    public function actionDelete(int $id): void
    {
        if (!$this->isAllowedGroup($id)) {
            $this->flashMessage('Nemáte oprávnění ke smazání této skupiny.', 'error');
            $this->redirect('default');
        }
        $this->groupFacade->deleteGroup($id);
        $this->flashMessage('Skupina byla smazána.');
        $this->redirect('default', ['id' => null]);
    }

    /**
     * AJAX signal to toggle group right
     */
    public function handleToggleRight(?int $rightId = null, ?bool $state = null): void
    {
        if (!$this->id) {
            $this->error('ID skupiny nebylo předáno.');
        }

        if (!$this->isAllowedGroup((int)$this->id)) {
            $this->error('Nemáte oprávnění k úpravě této skupiny.');
        }
        
        // If not in URL, try to get from request (AJAX data)
        if ($rightId === null) {
            $rightId = (int)$this->getHttpRequest()->getQuery('rightId');
        }
        if ($state === null) {
            $state = (bool)$this->getHttpRequest()->getQuery('state');
        }

        if (!$rightId) {
            $this->error('ID práva nebylo předáno.');
        }

        $this->rightFacade->toggleGroupRight((int)$this->id, $rightId, $state);
        
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
        $form->addHidden('admin_group_id');
        
        $groups = [];
        $userGroupId = (int)$this->loggedUserEntity->getAdminGroupId();
        
        // Only allow root if user is in group 1 (Superadmin)
        if ($userGroupId === 1) {
            $groups[0] = '-- Hlavní skupina --';
        }

        foreach ($this->groupFacade->getAvailableGroups($userGroupId) as $g) {
            if ($this->id && $g->admin_group_id == $this->id) continue;
            $groups[$g->admin_group_id] = $g->admin_group_name;
        }

        $form->addSelect('pid', 'Nadřazená skupina', $groups)
            ->setRequired('Zvolte nadřazenou skupinu');

        $form->addText('admin_group_name', 'Název skupiny')
            ->setRequired('Zadejte název skupiny');
        
        $form->addText('code_name', 'Kódové označení');
        
        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = [$this, 'groupFormSucceeded'];
        return $form;
    }

    public function groupFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int)$values->admin_group_id;
        
        if ($id && !$this->isAllowedGroup($id)) {
            $this->flashMessage('Nemáte oprávnění k úpravě této skupiny.', 'error');
            $this->redirect('default');
        }

        if (!$this->isAllowedGroup((int)$values->pid) && (int)$values->pid !== 0) {
            $this->flashMessage('Zvolená nadřazená skupina není povolena.', 'error');
            $this->redirect('default');
        }

        $entity = $id ? $this->groupFacade->getGroup($id) : new AdminGroupEntity();
        $entity->fillEntity((array)$values);
        
        $this->groupFacade->saveGroup($entity);
        $this->flashMessage('Skupina byla uložena.');
        $this->redirect('default');
    }
}
