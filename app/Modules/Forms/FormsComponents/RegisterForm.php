<?php

namespace App\Modules\Forms\FormsComponents;

use App\Modules\FormsData\Model\FormsDataEntity;
use App\Modules\FormsData\Model\FormsDataFacade;
use App\Modules\Members\Model\MembersEntity;
use App\Modules\Members\Model\MembersFacade;
use Nette\Application\UI\Form;

class RegisterForm extends BaseForm
{
    private MembersFacade $membersFacade;
    private bool $submitted = false;
    private bool $error;
    private array $formData = [];

    public int $memberId ;


    /** @persistent */
    public int $formElementId;

    /** @persistent */
    public int $page_id;

    public function __construct(FormsDataFacade $formsDataFacade, MembersFacade $membersFacade)
    {
        parent::__construct($formsDataFacade);
        $this->membersFacade = $membersFacade;
    }

    public function render(): void
    {
        $this->template->submitted = $this->submitted;
        $this->template->formData = $this->formData;
        $this->template->error = $this->error?? false;
        $this->template->setFile(__DIR__ . '/../templates/Forms/RegisterForm.latte');
        try {
            $this->template->render();
        } catch (\Throwable $e) {
            if ($this->getPresenter()->isAjax()) {
                // Pokud to v AJAXu lehne, pošleme chybu do payloadu,
                // abychom ji viděli v Network tabu
                $this->getPresenter()->getPayload()->error = $e->getMessage();
                $this->getPresenter()->sendPayload();
            } else {
                throw $e;
            }
        }
    }



    protected function createComponentForm(): Form
    {

        $form = new Form();
        $form->addText('degree', 'Titul');
        $form->addText('name', 'Jméno')
            ->setRequired('Zadejte prosím své jméno.');
        $form->addText('surname', 'Příjmení')
            ->setRequired('Zadejte prosím své příjmení.');
        $form->addEmail('email', 'E-mail')
            ->setRequired('Zadejte prosím svůj e-mail.');
        $form->addText('phone', 'Telefon');

        /* teoreteicky vypne js validaci
        >setHtmlAttribute('data-nette-rules', '[]') // Nette JS si neškrtne
        ->setHtmlAttribute('novalidate', 'novalidate');
        */

        $form->addText('birth_date', 'Datum narození')
            ->setHtmlType('date')
            ->setRequired('Zadejte prosím datum narození.');

        $form->addText('street', 'Ulice a č.p.')
            ->setRequired('Zadejte prosím ulici.');
        $form->addText('city', 'Město')
            ->setRequired('Zadejte prosím město.');
        $form->addText('zip', 'PSČ')
            ->setRequired('Zadejte prosím PSČ.');

        $form->addSubmit('send', 'Odeslat registraci');

        $form->onSuccess[] = [$this, 'formSucceeded'];
        $form->onError[] = [$this, 'formError'];


        return $form;
    }
    public function formError(Form $form): void
    {
        $this->error = true;
        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('registerForm'); // Překreslí formulář i s chybami
            $this->getPresenter()->redrawControl('flashes'); // Překreslí případné flashky
        }
    }

    public function formSucceeded(Form $form, $values): void
    {
        //todo remove
        $this->memberId = 33;
        $this->submitted = true;
        $this->formData = (array)$values;
        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('registerForm');
            //$this->getPresenter()->sendPayload();
            }
        return;


        // 1. Save to Members
        $member = new MembersEntity();
        $member->setDegree($values->degree);
        $member->setName($values->name);
        $member->setSurname($values->surname);
        $member->setEmail($values->email);
        $member->setPhone($values->phone);
        $member->setBirthDate($values->birth_date);
        $member->setStreet($values->street);
        $member->setCity($values->city);
        $member->setZip($values->zip);
        $member->setActive(1);
        $member->setSource(MembersEntity::SOURCE_WEB);



        $this->memberId = $this->membersFacade->saveMember($member);
        if($this->memberId){
            $this->membersFacade->generateQr($member);
            $this->membersFacade->generateRegistrationConfirmation($member);
            $this->membersFacade->sendRegistrationEmail($member);
        }


        // 2. Save to FormsData (Log)
        $entity = new FormsDataEntity();
        $entity->setFormName('Registrační formulář');
        $entity->setData((array)$values);
        $entity->setIpAddress($this->getPresenter()->getHttpRequest()->getRemoteAddress());
        $entity->setStatus(1); // New

        $this->formsDataFacade->saveFormData($entity);

        // 3. Prepare for redraw
        $this->submitted = true;
        $this->formData = (array)$values;
        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('registerForm');
            $form->reset();
        } else {
            $this->getPresenter()->flashMessage('Registrace byla úspěšně odeslána. Děkujeme!', 'success');
            $this->redirect('this');
        }
    }
}

interface IRegisterFormFactory
{
    public function create(): RegisterForm;
}
