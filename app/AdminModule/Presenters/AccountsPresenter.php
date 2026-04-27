<?php

namespace App\AdminModule\Presenters;

use App\Model\Admin\AdminFacade;
use App\Model\Admin\AdministratorEntity;
use App\Model\Lookup\LookupFacade;
use App\Model\Presentation\PresentationFacade;
use Nette\Application\UI\Form;

final class AccountsPresenter extends AdminPresenter
{
    /** @var LookupFacade @inject */
    public $lookupFacade;

    /** @var PresentationFacade @inject */
    public PresentationFacade $presentationFacade;

    /** @var int|null @persistent */
    public ?int $id;

    public function actionDefault(): void
    {
        $this->id = null;
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Účty';
        $userGroupId = (int)$this->loggedUserEntity->getAdminGroupId();
        $allowedGroupIds = array_keys($this->groupFacade->getAvailableGroups($userGroupId));
        $this->template->accounts = $this->adminFacade->getActiveAdmins($allowedGroupIds);
    }

    public function renderEdit(?int $id = null): void
    {
        if ($id and !$this->isAllowedAdmin($id)) {
            $this->flashMessage('Nemáte oprávnění k editaci tohoto účtu.', 'error');
            $this->redirect('default');
        }

        $this->template->title = $id ? 'Editace účtu' : 'Nový účet';
        $this->template->accountId = $id;

        if ($id) {
            $admin = $this->adminFacade->getAdmin($id);
            if (!$admin) {
                $this->error('Účet nebyl nalezen');
            }

            $data = $admin->getEntityData();
            unset($data['user_password']); // Don't show password in form
            $this['accountForm']->setDefaults($data);
        }
    }

    public function actionDelete(int $id): void
    {
        if (!$this->isAllowedAdmin($id)) {
            $this->flashMessage('Nemáte oprávnění ke smazání tohoto účtu.', 'error');
            $this->redirect('default');
        }
        $this->adminFacade->softDelete($id);
        $this->flashMessage('Účet byl odstraněn.');
        $this->redirect('default');
    }

    protected function createComponentAccountForm(): Form
    {
        $form = new Form;
        $form->addHidden('admin_id');

        $form->addText('user_name', 'Uživatelské jméno')
            ->setRequired('Zadejte uživatelské jméno');

        $form->addPassword('user_password', 'Heslo')
            ->setEmptyValue('')
            ->setNullable();

        $form->addText('name', 'Jméno');
        $form->addText('surname', 'Příjmení');
        $form->addEmail('email', 'Email')
            ->setRequired('Zadejte email');
        $form->addText('phone', 'Telefon');

        // Current admin language for translations
        $adminLang = $this->loggedUserEntity->admin_lang ?? C_LANGUAGE_CS;

        // Status from lookup
        $statusOptions = $this->lookupFacade->getLookupListOption(C_ADMINISTRATOR_STATUS, $adminLang);

        $form->addSelect('status', 'Status', $statusOptions)
            ->setRequired('Zvolte status');

        // Lang from lookup
        $langOptions = $this->lookupFacade->getLookupListOption(C_LANGUAGE, $adminLang);

        $form->addSelect('admin_lang', 'Jazyk', $langOptions)
            ->setRequired('Zvolte jazyk');

        $form->addSubmit('send', 'Uložit základní údaje')
            ->setHtmlAttribute('class', 'btn btn-success');

        $form->onSuccess[] = $this->accountFormSucceeded(...);
        return $form;
    }

    public function accountFormSucceeded(Form $form, \stdClass $values): void
    {
        $id = (int) $values->admin_id;

        if ($id and !$this->isAllowedAdmin($id)) {
            $this->flashMessage('Nemáte oprávnění k úpravě tohoto účtu.', 'error');
            $this->redirect('default');
        }

        $admin = $id ? $this->adminFacade->getAdmin($id) : new AdministratorEntity();

        if (empty($values->user_password)) {
            unset($values->user_password);
        } else {
           
            $hash = $this->adminFacade->createPassswordFromString($values->user_password);
            $admin->setPassword($hash);
            $admin->setUserPassSalt( $this->adminFacade->generateSalt());
            unset($values->user_password);
        }

        $admin->fillEntity((array) $values);

        $newId = $this->adminFacade->saveAdmin($admin);
        $this->flashMessage('Údaje byly uloženy.');

        if (!$id) {
            $this->redirect('edit', ['id' => $newId]);
        }
        $this->redirect('this');
    }

    protected function createComponentGroupsForm(): Form
    {
        $form = new Form;
        $userGroupId = (int)$this->loggedUserEntity->getAdminGroupId();
        $allowedGroups = $this->groupFacade->getAvailableGroups($userGroupId);

        $groupOptions = [];
        foreach ($allowedGroups as $g) {
            $groupOptions[$g->admin_group_id] = $g->admin_group_name;
        }

        $form->addRadioList('admin_group_id', 'Skupina', $groupOptions)
            ->setRequired('Zvolte skupinu');

        if ($this->id) {
            $admin = $this->adminFacade->getAdmin((int)$this->id);
            if ($admin) {
                $form['admin_group_id']->setDefaultValue($admin->admin_group_id);
            }
        }

        $form->addSubmit('send', 'Uložit skupinu')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = function(Form $form, $values) {
            if ($this->id) {
                if (!$this->isAllowedAdmin((int)$this->id)) {
                    $this->flashMessage('Nemáte oprávnění k úpravě tohoto účtu.', 'error');
                    $this->redirect('default');
                }

                if (!$this->isAllowedGroup((int)$values->admin_group_id)) {
                    $this->flashMessage('Zvolená skupina není povolena.', 'error');
                    $this->redirect('this');
                }

                $admin = $this->adminFacade->getAdmin((int)$this->id);
                if ($admin) {
                    $admin->admin_group_id = (int)$values->admin_group_id;
                    $this->adminFacade->saveAdmin($admin);
                    $this->flashMessage('Skupina byla uložena.');
                }
            }
            $this->redirect('this#tab-groups');
        };
        return $form;
    }

    protected function createComponentPresentationsForm(): Form
    {
        $form = new Form;
        $presentations = $this->presentationFacade->getPresentations();
        $presOptions = [];
        foreach ($presentations as $p) {
            $presOptions[$p->presentation_id] = $p->presentation_name . ' (' . $p->domain . ')';
        }

        $form->addCheckboxList('presentations', 'Prezentace', $presOptions);

        if ($this->id) {
            $form['presentations']->setDefaultValue($this->adminFacade->getAdminPresentations((int)$this->id));
        }

        $form->addSubmit('send', 'Uložit přístupy')
            ->setHtmlAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = function(Form $form, $values) {
            if ($this->id) {
                if (!$this->isAllowedAdmin((int)$this->id)) {
                    $this->flashMessage('Nemáte oprávnění k úpravě tohoto účtu.', 'error');
                    $this->redirect('default');
                }
                $this->adminFacade->saveAdminPresentations((int)$this->id, (array)$values->presentations);
                $this->flashMessage('Přístupy k prezentacím byly uloženy.');
            }
            $this->redirect('this#tab-presentations');
        };
        return $form;
    }
}
