<?php

namespace App\Modules\Forms\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Helper\IToolsControl;
use App\Modules\Forms\Model\FormsFacade;
use App\Modules\Forms\Model\FormsEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class FormsAdminControl extends Control implements IToolsControl
{
    private FormsFacade $facade;

    /** @var int|null @persistent */
    public $id = null;

    /** @var int @persistent */
    public $page = 1;

    /** @var string|null @persistent */
    public $search = null;

    /** @var string @persistent */
    public $view = 'list';

    /** @var string|null */
    public $code = null;

    public LoggedUserEntity $loggedUser;

    public function __construct(FormsFacade $facade, LoggedUserEntity $loggedUser)
    {
        $this->facade = $facade;
        $this->loggedUser = $loggedUser;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function render(): void
    {
        $this->template->loggedUserEntity = $this->loggedUser;

        if ($this->view === 'detail') {
            $this->renderDetail();
            return;
        }

        $this->renderList();
    }

    public function renderList(): void
    {
        $limit = 20;
        $offset = ($this->page - 1) * $limit;

        $items = $this->facade->findForms($limit, $offset, $this->search);
        $totalCount = $this->facade->countForms($this->search);

        $this->template->items = $items;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderDetail(): void
    {
        $item = $this->facade->getForm($this->id);
        if (!$item) {
            $this->handleList();
            return;
        }

        $this->template->item = $item;
        $this->template->setFile(__DIR__ . '/../templates/Admin/detail.latte');
        $this->template->render();
    }

    /* --- SIGNALS --- */

    public function handleList(): void
    {
        $this->view = 'list';
        $this->id = null;
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('forms');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDetail(int $id): void
    {
        $this->view = 'detail';
        $this->id = $id;
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('forms');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDelete(int $id): void
    {
        $this->facade->deleteForm($id);
        $this->getPresenter()->flashMessage('Záznam byl smazán.', 'success');
        if ($this->getPresenter()->isAjax()) {
            $this->getPresenter()->redrawControl('flashes');
            $this->handleList();
        } else {
            $this->redirect('this');
        }
    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;
        $form->addText('search', 'Hledat')
            ->setHtmlAttribute('placeholder', 'Název, IP, data...');
        $form->addSubmit('send', 'Hledat');
        $form->setDefaults(['search' => $this->search]);
        $form->onSuccess[] = function (Form $form, $values) {
            $this->search = $values->search;
            $this->page = 1;
            $this->view = 'list';

            if ($this->getPresenter()->isAjax()) {
                $this->redrawControl('forms');
            } else {
                $this->redirect('this');
            }
        };
        return $form;
    }
}

interface IFormsAdminControlFactory
{
    public function create(): FormsAdminControl;
}
