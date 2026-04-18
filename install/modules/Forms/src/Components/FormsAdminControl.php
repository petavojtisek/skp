<?php

namespace App\Modules\Forms\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Element\ElementEntity;
use App\Model\Element\ElementFacade;
use App\Model\Helper\IObjectControl;
use App\Model\Lookup\LookupFacade;
use App\Modules\Forms\Model\FormsEntity;
use App\Modules\Forms\Model\FormsFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\Finder;


class FormsAdminControl extends Control implements IObjectControl
{
    /** @persistent */
    public string $view = 'list';

    /** @persistent */
    public ?int $elementId = null;

    private int $componentId;
    private string $name;
    private string $code;

    private FormsFacade $facade;
    private ElementFacade $elementFacade;
    private LookupFacade $lookupFacade;
    private User $user;
    private LoggedUserEntity $loggedUserEntity;

    public function __construct(
        FormsFacade $facade,
        ElementFacade $elementFacade,
        LookupFacade $lookupFacade,
        User $user,
        LoggedUserEntity $loggedUser
    ) {
        $this->facade = $facade;
        $this->elementFacade = $elementFacade;
        $this->lookupFacade = $lookupFacade;
        $this->user = $user;
        $this->loggedUserEntity = $loggedUser;
    }

    public function setComponentId(int $componentId): void
    {
        $this->componentId = $componentId;
    }

    public function setComponentInfo(string $name, string $code): void
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function render(): void
    {
        $this->template->loggedUserEntity = $this->loggedUserEntity;
        $this->template->name = $this->name;
        $this->template->code = $this->code;
        $this->template->componentId = $this->componentId;
        $this->template->view = $this->view;

        if ($this->view === 'edit') {
            $item = $this->elementId ? $this->elementFacade->find($this->elementId) : new ElementEntity();
            $this->template->item = $item;
            $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
        } else {
            $this->template->items = $this->facade->getByComponentId($this->componentId);
            $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        }

        $this->template->render();
    }

    public function handleDelete(int $elementId): void
    {
        $this->facade->deleteForm($elementId);
        $this->elementFacade->delete($elementId);
        $this->getPresenter()->flashMessage("Formulář byl odstraněn ze stránky.", 'success');

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('forms');
            $this->getPresenter()->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    public function handleEdit(?int $elementId = null): void
    {
        $this->view = 'edit';
        $this->elementId = $elementId;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('forms');
        } else {
            $this->redirect('this');
        }
    }

    public function handleBack(): void
    {
        $this->view = 'list';
        $this->elementId = null;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('forms');
        } else {
            $this->redirect('this');
        }
    }

    public function handleRemoveFromPage(): void
    {
        $this->getPresenter()->handleRemoveFromPage($this->componentId, (int)$this->getPresenter()->id);
    }

    public function handleDeleteFromPresentation(): void
    {
        $items = $this->facade->getByComponentId($this->componentId);
        foreach ($items as $item) {
            $this->facade->deleteForm($item->getId());
            $this->elementFacade->delete($item->getId());
        }
        $this->getPresenter()->handleDeleteFromPresentation($this->componentId, (int)$this->getPresenter()->id);
    }

    protected function createComponentEditForm(): Form
    {
        $form = new Form();
        $form->addHidden('element_id');

        $form->addText('name', 'Interní název')
            ->setRequired('Zadejte interní název');

        $statuses = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);
        $form->addSelect('status_id', 'Stav', $statuses)
            ->setRequired('Vyberte stav');

        $files = $this->listDirs();


        $form->addSelect('form_component', 'Typ formuláře',$files)->setPrompt('-- Vyberte formulář --')
          ->setRequired('Vyberte typ formuláře');

        $form->addText('valid_from', 'Platnost od')->setHtmlType('date');
        $form->addText('valid_to', 'Platnost do')->setHtmlType('date');

        $form->addSubmit('save', 'Uložit nastavení formuláře');

        if ($this->elementId) {
            $element = $this->elementFacade->find($this->elementId);
            $formData = $this->facade->getForm($this->elementId);
            if ($element && $formData) {
                $values = $element->getEntityData();
                $values['form_component'] = $formData->getFormComponent();

                if ($element->getValidFrom()) $values['valid_from'] = $element->getValidFrom('Y-m-d');
                if ($element->getValidTo()) $values['valid_to'] = $element->getValidTo('Y-m-d');

                $form->setDefaults($values);
            }
        }

        $form->onSuccess[] = [$this, 'editFormSucceeded'];
        return $form;
    }


    protected function listDirs() : ?array
    {
        $res = [];
        $dirPath = __DIR__ . '/../templates/Forms/';
        $files =   Finder::findFiles('*.latte')
            ->in($dirPath);

        if($files){
            foreach ($files as $file){

                $res[$file->getBasename('.latte')]=$file->getBasename('.latte');
            }
        }

        return $res;
    }

    public function editFormSucceeded(Form $form, array $values): void
    {
        $id = $values['element_id'] ? (int)$values['element_id'] : null;

        $element = $id ? $this->elementFacade->find($id) : new ElementEntity();
        $element->setComponentId($this->componentId);
        $element->setName($values['name']);
        $element->setStatusId($values['status_id']);
        $element->setValidFrom($values['valid_from'] ?: null);
        $element->setValidTo($values['valid_to'] ?: null);

        $elementId = $this->elementFacade->save($element, $this->user->getId());


        $formData = $id ? $this->facade->getForm($id) : new FormsEntity();
        $formData->setId($elementId);
        $formData->setFormComponent($values['form_component']);

        $this->facade->saveForm($formData);

        $this->getPresenter()->flashMessage('Nastavení formuláře bylo uloženo.', 'success');

        $this->view = 'list';
        $this->elementId = null;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl('forms');
            $this->getPresenter()->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }
}

interface IFormsAdminControlFactory
{
    public function create(): FormsAdminControl;
}
