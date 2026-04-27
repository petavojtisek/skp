<?php

namespace App\Modules\FormsData\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Emails\EmailsFacade;
use App\Modules\FormsData\Model\FormsDataFacade;
use Nette\Application\UI\Control;

class FormsDataDashboardControl extends Control
{
    private FormsDataFacade $facade;

    public LoggedUserEntity $loggedUser;

    private EmailsFacade $emailsFacade;
    private string $formName;
    private int $limit;

    /** @var int @persistant */
    public ?int $id = null;

    public string $code = 'formsDataDashboard';
    public function __construct(
        FormsDataFacade $facade,
        LoggedUserEntity $loggedUser,
        EmailsFacade $emailsFacade,
        string $formName = 'Kontaktní formulář',
        int $limit = 5

    )
    {
        $this->facade = $facade;
        $this->formName = $formName;
        $this->limit = $limit;
        $this->loggedUser = $loggedUser;
        $this->emailsFacade = $emailsFacade;
    }

    public function render(): void
    {

        $this->template->loggedUserEntity = $this->loggedUser;


        if($this->id) {
            $this->renderDetail();
            return;
        }
        $this->template->items = $this->facade->findLastByFormName($this->formName, $this->limit);
        $this->template->setFile(__DIR__ . '/../templates/Dashboard/inquiries.latte');
        $this->template->toolUri = $this->getPresenter()->link(':Admin:Tools:default', ['do'=>'FormsData-list']);
        $this->template->render();

    }


    public function renderDetail()
    {
        $item = $this->facade->getFormData($this->id);
        if (!$item) {
            $this->handleList();
            return;
        }

        $this->template->item = $item;
        $this->template->setFile(__DIR__ . '/../templates/Dashboard/detail.latte');
        $this->template->render();
    }

    public function handleDetail(int $id)
    {
        //$this->view = 'detail';
        $this->id = $id;
        $presenter = $this->getPresenter();
        $presenter->activeControl = $this->code;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('dashboard');
           // $this->redrawControl('inq');
        } else {
            $this->redirect('this');
        }
    }

    public function handleList()
    {
        $this->id = null;
        $presenter = $this->getPresenter();
        $presenter->activeControl = null;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('dashboard');
        }
    }

    public function handleDelete(int $id): void
    {

        $this->facade->deleteFormData($id);
        $this->getPresenter()->flashMessage('Záznam byl smazán.', 'success');

        $presenter = $this->getPresenter();
        $presenter->activeControl = null;
        if ($presenter->isAjax()) {
            $presenter->redrawControl('dashboard');
            $this->redrawControl('inquiryList');
        } else {
            $this->redirect('this');
        }
    }

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
            $this->getPresenter()->redrawControl('dashboard');
            $this->redrawControl('inquiryDetail');
        } else {
            $this->redirect('this');
        }
    }
}

interface IFormsDataDashboardControlFactory
{
    public function create(string $formName = 'Kontaktní formulář', int $limit = 5): FormsDataDashboardControl;
}
