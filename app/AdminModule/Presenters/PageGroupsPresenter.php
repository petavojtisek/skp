<?php

namespace App\AdminModule\Presenters;

use App\Model\PageGroup\PageGroupFacade;
use App\Model\PageGroup\PageGroupEntity;
use App\Model\AdminGroup\AdminGroupFacade;
use Nette\Application\UI\Form;

final class PageGroupsPresenter extends AdminPresenter
{
    /** @var PageGroupFacade @inject */
    public $pageGroupFacade;

    /** @var AdminGroupFacade @inject */
    public $adminGroupFacade;

    /** @var int|null @persistent */
    public $id;

    public function actionDefault(): void
    {
        $this->id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Skupiny stránek';
        $this->template->pageGroups = $this->pageGroupFacade->getPageGroups();
    }

    public function renderEdit(?int $id = null): void
    {
        if ($id === null && $this->id !== null) {
            $id = (int)$this->id;
        }

        $this->template->title = $id ? 'Editace skupiny stránek' : 'Nová skupina stránek';
        $this->template->id = $id;

        if ($id) {
            $pageGroup = $this->pageGroupFacade->getPageGroup($id);
            if (!$pageGroup) {
                $this->error('Skupina stránek nebyla nalezena');
            }
            $this['pageGroupForm']->setDefaults([
                'id' => $pageGroup->id,
                'name' => $pageGroup->name,
            ]);

            $this->template->allAdminGroups = $this->adminGroupFacade->getGroups();
            $this->template->activeAdminGroupIds = $this->pageGroupFacade->getAdminGroupIds($id);
        } else {
            $this->template->allAdminGroups = [];
            $this->template->activeAdminGroupIds = [];
        }
    }

    public function handleToggleAdminGroup(?int $adminGroupId = null, ?bool $state = null): void
    {
        if (!$this->id) {
            $this->error('ID skupiny stránek nebylo předáno.');
        }

        if ($adminGroupId === null) {
            $adminGroupId = (int)$this->getHttpRequest()->getQuery('adminGroupId');
        }
        if ($state === null) {
            $state = (bool)$this->getHttpRequest()->getQuery('state');
        }

        if (!$adminGroupId) {
            $this->error('ID administrátorské skupiny nebylo předáno.');
        }

        $this->pageGroupFacade->toggleAdminGroup($this->id, $adminGroupId, $state);
        
        if ($this->isAjax()) {
            $this->flashMessage('Přístup byl aktualizován.');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    public function actionDelete(int $id): void
    {
        $this->pageGroupFacade->deletePageGroup($id);
        $this->flashMessage('Skupina stránek byla smazána.');
        $this->redirect('default');
    }

    protected function createComponentPageGroupForm(): Form
    {
        $form = new Form;
        $form->addHidden('id');
        $form->addText('name', 'Název')
            ->setRequired('Zadejte název');
        
        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = [$this, 'pageGroupFormSucceeded'];
        return $form;
    }

    public function pageGroupFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int)$values->id;
        $entity = $id ? $this->pageGroupFacade->getPageGroup($id) : new PageGroupEntity();
        $entity->fillEntity((array)$values);
        
        $newId = $this->pageGroupFacade->savePageGroup($entity);
        $this->flashMessage('Skupina stránek byla uložena.');
        
        if (!$id) {
            $this->redirect('edit', ['id' => $newId]);
        } else {
            $this->redirect('default');
        }
    }
}
