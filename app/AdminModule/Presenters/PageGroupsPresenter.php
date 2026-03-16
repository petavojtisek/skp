<?php

namespace App\AdminModule\Presenters;

use App\Model\PageGroup\PageGroupFacade;
use Nette\Application\UI\Form;

final class PageGroupsPresenter extends AdminPresenter
{
    /** @var PageGroupFacade @inject */
    public $pageGroupFacade;

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
        $this->template->title = $id ? 'Editace skupiny stránek' : 'Nová skupina stránek';
        // Skeleton
    }

    public function actionDelete(int $id): void
    {
        // Skeleton
        $this->flashMessage('Skupina stránek byla smazána.');
        $this->redirect('default');
    }

    protected function createComponentPageGroupForm(): Form
    {
        $form = new Form;
        $form->addHidden('page_group_id');
        $form->addText('page_group_name', 'Název')
            ->setRequired('Zadejte název');
        
        $form->addSubmit('send', 'Uložit');
        $form->onSuccess[] = [$this, 'pageGroupFormSucceeded'];
        return $form;
    }

    public function pageGroupFormSucceeded(Form $form, \stdClass $values): void
    {
        // Skeleton
        $this->flashMessage('Skupina stránek byla uložena.');
        $this->redirect('default');
    }
}
