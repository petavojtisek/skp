<?php

namespace App\AdminModule\Presenters;

use App\Model\Login\LoginFacade;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignPresenter extends AdminPresenter
{
    /** @var LoginFacade @inject */
    public $loginFacade;

    /** @var \App\Model\Log\LogFacade @inject */
    public $logFacade;

    public function startup(): void
    {
        parent::startup();
        $this->loginFacade->setStorage('admin');
    }

    public function actionDefault(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Dashboard:');
        }
    }

    public function actionOut(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->logFacade->logAction('System', 'LOGOUT', 'Odhlášení uživatele: ' . $this->getUser()->getIdentity()->user_name, (int)$this->getUser()->getId());
        }
        $this->getUser()->logout();
        $this->flashMessage('Byli jste odhlášeni.');
        $this->redirect('in');
    }

    protected function createComponentLoginForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired('Zadejte uživatelské jméno');

        $form->addPassword('password', 'Heslo')
            ->setRequired('Zadejte heslo');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = $this->loginFormSucceeded(...);
        return $form;
    }

    public function loginFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->loginFacade->login($values->username, $values->password);
            $this->redirect('Dashboard:');

        } catch (AuthenticationException) {
            $form->addError('Nesprávné jméno nebo heslo.');
        }
    }
}
