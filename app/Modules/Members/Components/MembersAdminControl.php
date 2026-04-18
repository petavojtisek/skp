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

    /** @var string|null @persistent */
    public $source = null;

    /** @var string|null @persistent */
    public $registrationEmail = null;

    /** @var string|null @persistent */
    public $registrationConfirm = null;

    /** @var string|null @persistent */
    public $paymentConfirm = null;

    /** @var string|null @persistent */
    public $isPaid = null;

    /** @var string|null @persistent */
    public $activeStatus = null;

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

        $items = $this->facade->findMembers(
            $limit, 
            $offset, 
            $this->search, 
            $this->source,
            $this->registrationEmail === null ? null : (bool)$this->registrationEmail,
            $this->registrationConfirm === null ? null : (bool)$this->registrationConfirm,
            $this->paymentConfirm === null ? null : (bool)$this->paymentConfirm,
            $this->isPaid === null ? null : (bool)$this->isPaid,
            $this->activeStatus === null ? null : (bool)$this->activeStatus
        );
        $totalCount = $this->facade->countMembers(
            $this->search, 
            $this->source,
            $this->registrationEmail === null ? null : (bool)$this->registrationEmail,
            $this->registrationConfirm === null ? null : (bool)$this->registrationConfirm,
            $this->paymentConfirm === null ? null : (bool)$this->paymentConfirm,
            $this->isPaid === null ? null : (bool)$this->isPaid,
            $this->activeStatus === null ? null : (bool)$this->activeStatus
        );

        $this->template->items = $items;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search;
        $this->template->source = $this->source;
        $this->template->registrationEmail = $this->registrationEmail;
        $this->template->registrationConfirm = $this->registrationConfirm;
        $this->template->paymentConfirm = $this->paymentConfirm;
        $this->template->isPaid = $this->isPaid;
        $this->template->activeStatus = $this->activeStatus;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderEdit(): void
    {
        if ($this->id && !$this->getComponent('memberForm')->isSubmitted()) {
            $item = $this->facade->getMember($this->id);
            if ($item) {
                $values = $item->getEntityData();
                if ($item->getBirthDate()) $values['birth_date'] = $item->getBirthDate('Y-m-d');
                if ($item->getLastMemberPayment()) $values['last_member_payment'] = $item->getLastMemberPayment('Y-m-d');
                
                // Formátování nových polí pro readonly zobrazení
                if ($item->getRegistrationEmailDt()) $values['registration_email_dt'] = $item->getRegistrationEmailDt();
                if ($item->getRegistrationConfirmEmailDt()) $values['registration_confirm_email_dt'] = $item->getRegistrationConfirmEmailDt();
                if ($item->getPaymentConfirmEmailDt()) $values['payment_confirm_email_dt'] = $item->getPaymentConfirmEmailDt();
                if ($item->getPaymentReminderEmailDt()) $values['payment_reminder_email_dt'] = $item->getPaymentReminderEmailDt();
                if ($item->getPaymentRenewEmailDt()) $values['payment_renew_email_dt'] = $item->getPaymentRenewEmailDt();
                if ($item->getCreatedDt()) $values['created_dt'] = $item->getCreatedDt('d.m.Y H:i:s');

                $this['memberForm']->setDefaults($values);
            }
        }
        $this->template->id = $this->id;
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
        $this->getPresenter()->redrawControl('flashes');
        $this->handleList();
    }

    public function handleExport(mixed $ids = null): void
    {
        // Podpora pro vše (null), pole (array) i jedno ID (int/string)
        bdump($ids, 'Export - vstupní IDs');
        $this->getPresenter()->terminate();
    }

    public function handleSendEmail(mixed $ids = null, ?string $subject = null, ?string $content = null): void
    {
        bdump(['ids' => $ids, 'subject' => $subject, 'content' => $content], 'Odesílání e-mailu');
        $this->getPresenter()->flashMessage('E-maily byly zařazeny k odeslání.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    public function handleSetPaymentDate(mixed $ids = null, ?string $date = null): void
    {
        bdump(['ids' => $ids, 'date' => $date], 'Nastavení data platby');
        $this->getPresenter()->flashMessage('Datum platby bylo u vybraných členů aktualizováno.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    public function handleSendRegistrationEmail(mixed $ids = null): void
    {
        bdump($ids, 'Odesílání registračního e-mailu');
        $this->getPresenter()->flashMessage('Registrační e-maily byly zařazeny k odeslání.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    public function handleSendAcceptanceEmail(mixed $ids = null): void
    {
        bdump($ids, 'Odesílání potvrzení o přijetí');
        $this->getPresenter()->flashMessage('E-maily o přijetí do spolku byly zařazeny k odeslání.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    public function handleSendPaymentConfirmation(mixed $ids = null): void
    {
        bdump($ids, 'Odesílání potvrzení platby');
        $this->getPresenter()->flashMessage('Potvrzení o platbě byla zařazena k odeslání.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    public function handleSendPaymentReminder(mixed $ids = null): void
    {
        bdump($ids, 'Odesílání upomínky platby');
        $this->getPresenter()->flashMessage('Upomínky na platbu byly zařazeny k odeslání.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->getPresenter()->terminate();
    }

    /* --- COMPONENTS --- */

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;
        $form->addText('search', 'Hledat')
            ->setHtmlAttribute('placeholder', 'Jméno, příjmení, číslo, email...');
        
        $form->addSelect('source', 'Zdroj', MembersEntity::SOURCES)
            ->setPrompt('Všechny zdroje');

        $options = [1 => 'Ano', 0 => 'Ne'];

        $form->addSelect('registrationEmail', 'Reg. email', $options)
            ->setPrompt('?');

        $form->addSelect('registrationConfirm', 'Poděkování', $options)
            ->setPrompt('?');

        $form->addSelect('paymentConfirm', 'Potvrz. platby', $options)
            ->setPrompt('?');

        $form->addSelect('isPaid', 'Zaplaceno', $options)
            ->setPrompt('?');

        $form->addSelect('activeStatus', 'Aktivní', $options)
            ->setPrompt('?');

        $form->addSubmit('send', 'Hledat');
        $form->setDefaults([
            'search' => $this->search,
            'source' => $this->source,
            'registrationEmail' => $this->registrationEmail,
            'registrationConfirm' => $this->registrationConfirm,
            'paymentConfirm' => $this->paymentConfirm,
            'isPaid' => $this->isPaid,
            'activeStatus' => $this->activeStatus
        ]);
        $form->onSuccess[] = function (Form $form, $values) {
            $this->search = $values->search;
            $this->source = $values->source;
            $this->registrationEmail = $values->registrationEmail;
            $this->registrationConfirm = $values->registrationConfirm;
            $this->paymentConfirm = $values->paymentConfirm;
            $this->isPaid = $values->isPaid;
            $this->activeStatus = $values->activeStatus;
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

        $form->addText('street', 'Ulice a č.p.');
        $form->addText('city', 'Město');
        $form->addText('zip', 'PSČ');

        $form->addText('last_member_payment', 'Poslední platba')
            ->setHtmlType('date');

        $form->addCheckbox('active', 'Aktivní')
            ->setDefaultValue(true);

        $form->addTextArea('note', 'Poznámka')
            ->setHtmlAttribute('rows', 3);

        $form->addText('source', 'Zdroj')
            ->setHtmlAttribute('readonly', true);

        $form->addText('registration_email_dt', 'Email s registrací')
            ->setHtmlAttribute('readonly', true);

        $form->addText('registration_confirm_email_dt', 'Potvrzení registrace')
            ->setHtmlAttribute('readonly', true);

        $form->addText('payment_confirm_email_dt', 'Potvrzení platby')
            ->setHtmlAttribute('readonly', true);

        $form->addText('payment_reminder_email_dt', 'Upomínka platby')
            ->setHtmlAttribute('readonly', true);

        $form->addText('payment_renew_email_dt', 'Obnovení členství')
            ->setHtmlAttribute('readonly', true);

        $form->addText('created_dt', 'Vytvořeno')
            ->setHtmlAttribute('readonly', true);

        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = [$this, 'memberFormSucceeded'];
        return $form;
    }

    public function memberFormSucceeded(Form $form, $values): void
    {
        if(empty($values->member_id)) {
            $values->member_id = 0;
        }

        $entity = new MembersEntity($values);
        $this->facade->saveMember($entity);
        $this->getPresenter()->flashMessage('Člen byl uložen.', 'success');
        $this->getPresenter()->redrawControl('flashes');
        $this->handleList();
    }
}

interface IMembersAdminControlFactory
{
    public function create(): MembersAdminControl;
}
