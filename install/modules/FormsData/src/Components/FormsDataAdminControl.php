<?php

namespace App\Modules\FormsData\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Emails\EmailsFacade;
use App\Model\Helper\IToolsControl;
use App\Modules\FormsData\Model\FormsDataFacade;
use App\Modules\FormsData\Model\FormsDataEntity;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class FormsDataAdminControl extends Control implements IToolsControl
{
    private FormsDataFacade $facade;

    private EmailsFacade $emailsFacade;

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

    public function __construct(FormsDataFacade $facade, LoggedUserEntity $loggedUser, EmailsFacade $emailsFacade)
    {
        $this->facade = $facade;
        $this->loggedUser = $loggedUser;
        $this->emailsFacade = $emailsFacade;
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

        $items = $this->facade->findFormsData($limit, $offset, $this->search);
        $totalCount = $this->facade->countFormsData($this->search);

        $this->template->items = $items;
        $this->template->page = $this->page;
        $this->template->lastPage = ceil($totalCount / $limit);
        $this->template->search = $this->search;

        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    public function renderDetail(): void
    {
        $item = $this->facade->getFormData($this->id);
        if (!$item) {
            $this->handleList();
            return;
        }

        $this->template->item = $item;
        $this->template->setFile(__DIR__ . '/../templates/Admin/detail.latte');
        $this->template->render();
    }

    /* --- SIGNALS --- */

    public function handleSendMail(?int $id = null, ?string $email = null, ?string $subject = null, ?string $content = null): void
    {
        // Fallback pro případ, že Nette ne namapovalo parametry automaticky z AJAX požadavku
        $id = $id ?? (int)$this->getPresenter()->getParameter('id');
        $email = $email ?? $this->getPresenter()->getParameter('email');
        $subject = $subject ?? $this->getPresenter()->getParameter('subject');
        $content = $content ?? $this->getPresenter()->getParameter('content');

        if (!$email || !$subject || !$content) {
             $this->getPresenter()->flashMessage("Chybí povinné údaje pro odeslání e-mailu.", 'danger');
        } else {
             $this->emailsFacade->sendGenericEmail($email, $subject, $content);
             
             // Uložení odpovědi do databáze
             if ($id) {
                 $formData = $this->facade->getFormData($id);
                 if ($formData) {
                     $formData->setResponse([
                         'email' => $email,
                         'subject' => $subject,
                         'content' => $content
                     ]);
                     $formData->setResponseDt(new \DateTime());
                     $this->facade->saveFormData($formData);
                 }
             }

             $this->getPresenter()->flashMessage("Email pro '$email' s předmětem '$subject' byl odeslán a uložen.", 'success');
        }

        if ($this->getPresenter()->isAjax()) {
            $this->getPresenter()->redrawControl('flashes');
            $this->redrawControl('forms_dataDetail');
        } else {
            $this->redirect('this');
        }
    }

    public function handleList(): void
    {
        $this->view = 'list';
        $this->id = null;
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('forms_data');
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
            $this->redrawControl('forms_dataDetail');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDelete(int $id): void
    {
        $this->facade->deleteFormData($id);
        $this->getPresenter()->flashMessage('Záznam byl smazán.', 'success');

        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('forms_data');
            $presenter->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    public function handleRender(): void
    {
        $this->view = 'default';
        $this->id = null;
        $presenter = $this->getPresenter();
        $presenter->activeControl = null;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('tools');
            $this->redrawControl('forms_data');
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
                $this->redrawControl('forms_data');
            } else {
                $this->redirect('this');
            }
        };
        return $form;
    }
}

interface IFormsDataAdminControlFactory
{
    public function create(): FormsDataAdminControl;
}
