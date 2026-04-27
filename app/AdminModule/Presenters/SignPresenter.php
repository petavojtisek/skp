<?php

namespace App\AdminModule\Presenters;

use App\Model\Log\LogFacade;
use App\Model\Login\LoginFacade;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignPresenter extends AdminPresenter
{
    /** @var LoginFacade @inject */
    public LoginFacade $loginFacade;

    /** @var \App\Model\Log\LogFacade @inject */
    public LogFacade $logFacade;

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

        // Remove admin_active cookie on logout
        $this->getHttpResponse()->deleteCookie('admin_active');

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

            // Set cookie for file picker bypass (lasts 1 day)
            $this->getHttpResponse()->setCookie('admin_active', (string)$this->getUser()->getId(), '1 day');

            $this->redirect('Dashboard:');

        } catch (AuthenticationException) {
            $form->addError('Nesprávné jméno nebo heslo.');
        }
    }
}
