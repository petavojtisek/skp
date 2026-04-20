<?php

namespace App\Modules\Forms\FormsComponents;

use App\Model\Emails\EmailsFacade;
use App\Modules\FormsData\Model\FormsDataEntity;
use App\Modules\FormsData\Model\FormsDataFacade;
use App\Modules\Members\Model\MembersFacade;
use Nette\Application\UI\Form;
class ContactForm extends BaseForm
{

    public ?bool $success;
    public ?bool $error;

    private EmailsFacade $emailFacade;

    public function __construct(FormsDataFacade $formsDataFacade, EmailsFacade $emailFacade)
    {
        parent::__construct($formsDataFacade);
        $this->emailFacade = $emailFacade;
    }

    public function render(): void
    {

        $this->template->setFile(__DIR__ . '/../templates/Forms/ContactForm.latte');
        $this->template->success = $this->success?? false;
        $this->template->error = $this->error?? false;
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->addText('name', 'Jméno a příjmení')
            ->setRequired('Zadejte prosím své jméno.');
        $form->addEmail('email', 'E-mail')
            ->setRequired('Zadejte prosím svůj e-mail.');
        $form->addTextArea('message', 'Zpráva')
            ->setRequired('Napište nám zprávu.');
        $form->addSubmit('send', 'Odeslat');

        $form->onSuccess[] = [$this, 'formSucceeded'];
        $form->onError[] = [$this, 'formError'];
        return $form;
    }

    public function formError(Form $form): void
    {
        $this->error = true;
        //$values = $form->getValues(true);
        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('contactForm'); // Překreslí formulář i s chybami
        }
    }

    public function formSucceeded(Form $form, $values): void
    {
        $entity = new FormsDataEntity();
        $entity->setFormName('Kontaktní formulář');
        $entity->setData((array)$values);
        $entity->setIpAddress($this->getPresenter()->getHttpRequest()->getRemoteAddress());
        $entity->setStatus(1); // New


        $this->formsDataFacade->saveFormData($entity);

        $this->emailFacade->sendGenericEmail($values['email'],'Potvrzení přijetí dotazu', '<p>Děkujeme za dotaz.</p><p>Váš dotaz se budeme snažit vyřídit co nejrychleji.</p>');
        $this->emailFacade->sendGenericEmail('skp@krajinapolabi.cz','Přijetí dotazu',"
            <p>Od: {$values['name']},  Email: {$values['email']}</p> 
            <p>{$values['message']}</p>

");

        if ($this->getPresenter()->isAjax()) {
            $this->success = true;
            $this->redrawControl('contactForm');
            $form->reset();
        } else {
            $this->getPresenter()->flashMessage('Vaše zpráva byla úspěšně odeslána. Děkujeme!', 'success');

            $this->redirect('this');
        }
    }
}

interface IContactFormFactory
{
    public function create(): ContactForm;
}
