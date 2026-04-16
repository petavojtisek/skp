<?php

namespace App\Modules\Forms\FormsComponents;

use App\Modules\FormsData\Model\FormsDataEntity;
use App\Modules\FormsData\Model\FormsDataFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class ContactForm extends Control
{
    private FormsDataFacade $formsDataFacade;

    public function __construct(FormsDataFacade $formsDataFacade)
    {
        $this->formsDataFacade = $formsDataFacade;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/../templates/Forms/ContactForm.latte');
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
        return $form;
    }

    public function formSucceeded(Form $form, $values): void
    {
        $entity = new FormsDataEntity();
        $entity->setFormName('Kontaktní formulář');
        $entity->setData((array)$values);
        $entity->setIpAddress($this->getPresenter()->getHttpRequest()->getRemoteAddress());
        $entity->setStatus(1); // New

        $this->formsDataFacade->saveFormData($entity);

        $this->getPresenter()->flashMessage('Vaše zpráva byla úspěšně odeslána. Děkujeme!', 'success');
        
        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('contactForm');
            $this->getPresenter()->redrawControl('flashes');
            $form->reset();
        } else {
            $this->redirect('this');
        }
    }
}

interface IContactFormFactory
{
    public function create(): ContactForm;
}
