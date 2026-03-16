<?php

namespace App\AdminModule\Presenters;

use App\Model\AdminGroup\AdminGroupFacade;
use App\Model\AdminGroup\AdminGroupEntity;
use App\Model\AdminRight\AdminRightFacade;
use Nette\Application\UI\Form;

final class GroupsPresenter extends AdminPresenter
{
    /** @var AdminGroupFacade @inject */
    public $groupFacade;

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
        $this->template->tree = $this->groupFacade->getGroupTree();
    }

    public function renderEdit(?int $id = null, ?int $parentId = null): void
    {
        if ($id === null && $this->id !== null) {
            $id = (int)$this->id;
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

        $this->rightFacade->toggleGroupRight($this->id, $rightId, $state);
        
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
        
        $groups = [0 => '-- Hlavní skupina --'];
        foreach ($this->groupFacade->getGroups() as $g) {
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
        $entity = $id ? $this->groupFacade->getGroup($id) : new AdminGroupEntity();
        $entity->fillEntity((array)$values);
        
        $this->groupFacade->saveGroup($entity);
        $this->flashMessage('Skupina byla uložena.');
        $this->redirect('default');
    }
}
