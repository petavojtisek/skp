<?php

namespace App\Modules\Members\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Helper\IToolsControl;
use App\Modules\Members\Model\MembersFacade;
use App\Modules\Members\Model\MembersEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class MembersAdminControl extends Control implements IToolsControl
{
    private MembersFacade $facade;

    /** @var int|null @persistent */
    public $id = null;

    /** @var int @persistent */
    public $page = 1;

    /** @var string|null @persistent */
    public $search = null;

    /** @var string @persistent */
    public $view = 'default';

    /** @var string|null */
    public $code = null;

    public LoggedUserEntity $loggedUser;

    public function __construct(MembersFacade $facade, LoggedUserEntity $loggedUser)
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
        } elseif($this->view === 'list') {
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

        $items = $this->facade->findMembers($limit, $offset,$this->search);
        $totalCount = $this->facade->countMembers($this->search);

        $this->template->items = $items;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderEdit(): void
    {
        if ($this->id && !$this->getComponent('memberForm')->isSubmitted()) {
            $item = $this->facade->getMember($this->id);
            if ($item) {
                $values = $item->toArray();
                if ($item->getBirthDate()) $values['birth_date'] = $item->getBirthDate('Y-m-d');
                if ($item->getLastMemberPayment()) $values['last_member_payment'] = $item->getLastMemberPayment('Y-m-d');
                $this['memberForm']->setDefaults($values);
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
            $this->redrawControl('members');
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
            $this->redrawControl('members');
        }
    }

    public function handleDelete(int $id): void
    {
        $this->facade->deleteMember($id);
        $this->getPresenter()->flashMessage('Člen byl smazán.', 'success');
        $this->handleList();
    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;
        $form->addText('search', 'Hledat')
            ->setHtmlAttribute('placeholder', 'Jméno, příjmení, číslo, email...');
        $form->addSubmit('send', 'Hledat');
        $form->setDefaults(['search' => $this->search]);
        $form->onSuccess[] = function (Form $form, $values) {
            xdebug_break();
            $this->search = $values->search;
            $this->page = 1;

            if ($this->getPresenter()->isAjax()) {
                $this->redrawControl('members');
            } else {
                $this->redirect('this');
            }
        };
        return $form;
    }

    protected function createComponentMemberForm(): Form
    {
        $form = new Form;
        $form->addHidden('member_id');

        $form->addText('member_number', 'Číslo člena')
            ->setHtmlAttribute('readonly', true)
            ->setHtmlAttribute('placeholder', 'Bude vygenerováno automaticky');

        $form->addText('degree', 'Titul');
        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte jméno');
        $form->addText('surname', 'Příjmení')
            ->setRequired('Zadejte příjmení');

        $form->addText('birth_date', 'Datum narození')
            ->setHtmlType('date');

        $form->addText('email', 'Email')
            ->addCondition(Form::FILLED)
                ->addRule(Form::EMAIL, 'Zadejte platný email');

        $form->addText('phone', 'Telefon');

        $form->addTextArea('address', 'Adresa')
            ->setHtmlAttribute('rows', 3);

        $form->addText('last_member_payment', 'Poslední platba')
            ->setHtmlType('date');

        $form->addCheckbox('active', 'Aktivní')
            ->setDefaultValue(true);

        $form->addTextArea('note', 'Poznámka')
            ->setHtmlAttribute('rows', 3);

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'memberFormSucceeded'];
        return $form;
    }

    public function memberFormSucceeded(Form $form, $values): void
    {
        $entity = new MembersEntity($values);
        $this->facade->saveMember($entity);
        $this->getPresenter()->flashMessage('Člen byl uložen.', 'success');
        $this->handleList();
    }
}

interface IMembersAdminControlFactory
{
    public function create(): MembersAdminControl;
}
