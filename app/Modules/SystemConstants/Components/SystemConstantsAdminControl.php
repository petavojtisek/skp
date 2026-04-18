<?php

namespace App\Modules\SystemConstants\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Helper\IToolsControl;
use App\Modules\SystemConstants\Model\SystemConstantsFacade;
use App\Modules\SystemConstants\Model\SystemConstantsEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class SystemConstantsAdminControl extends Control implements IToolsControl
{
    private SystemConstantsFacade $facade;

    /** @var int|null @persistent */
    public $id = null;

    /** @var int @persistent */
    public $page = 1;

    /** @var string|null */
    public $code = null;

    /** @var string|null @persistent */
    public $search = null;

    /** @var string @persistent */
    public $view = 'default';

    public LoggedUserEntity $loggedUser;

    public function __construct(SystemConstantsFacade $facade, LoggedUserEntity $loggedUser)
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

        if ($this->view === 'edit') {
            $this->renderEdit();
            return;
        } elseif ($this->view === 'list') {
            $this->renderList();
            return;
        }

        $this->template->setFile(__DIR__ . '/../templates/Admin/default.latte');
        $this->template->render();
    }

    public function renderList(): void
    {
        $limit = 20;
        $offset = ($this->page - 1) * $limit;

        $items = $this->facade->findSystemConstants($this->search, $limit, $offset);
        $totalCount = $this->facade->countSystemConstants($this->search);

        $this->template->items = $items;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderEdit(): void
    {
        if ($this->id && !$this->getComponent('systemConstantForm')->isSubmitted()) {
            $item = $this->facade->getSystemConstant($this->id);
            if ($item) {
                $this['systemConstantForm']->setDefaults($item->getEntityData());
            }
        }

        $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
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
            $this->redrawControl('systemConstants');
        }
    }

    public function handleEdit(?int $id = null): void
    {
        $this->view = 'edit';
        $this->id = $id;
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('systemConstantsEdit');
        }
    }

    public function handleDelete(int $id): void
    {
        $this->facade->deleteSystemConstant($id);
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('systemConstants');
        }

        $this->getPresenter()->flashMessage('Konstanta byla smazána.', 'success');
        $this->getPresenter()->redrawControl('flashes');
    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;
        $form->addText('search', 'Hledat')
            ->setHtmlAttribute('placeholder', 'Kód nebo hodnota...');
        $form->addSubmit('send', 'Hledat');
        $form->setDefaults(['search' => $this->search]);
        $form->onSuccess[] = function (Form $form, $values) {
            $this->search = $values->search;
            $this->page = 1;
            if ($this->getPresenter()->isAjax()) {
                $this->redrawControl('systemConstants');
            } else {
                $this->redirect('this');
            }
        };
        return $form;
    }

    protected function createComponentSystemConstantForm(): Form
    {
        $form = new Form;
        $form->addHidden('system_constant_id');
        $form->addText('code', 'Kód')
            ->setRequired('Zadejte kód konstanty');
        $form->addText('value', 'Hodnota')
            ->setRequired('Zadejte hodnotu'); // Use standard text input
        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'systemConstantFormSucceeded'];
        return $form;
    }

    public function systemConstantFormSucceeded(Form $form, $values): void
    {
        if (empty($values->system_constant_id)) {
            $values->system_constant_id = 0;
        }

        $entity = new SystemConstantsEntity((array)$values);
        $this->facade->saveSystemConstant($entity);
        $this->getPresenter()->flashMessage('Konstanta byla uložena.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->handleList();
    }
}

interface ISystemConstantsAdminControlFactory
{
    public function create(): SystemConstantsAdminControl;
}
