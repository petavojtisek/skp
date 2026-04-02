<?php

namespace App\Modules\ContentVersion\Components;

use App\AdminModule\Components\IObjectControl;
use App\Modules\ContentVersion\Model\ContentVersionFacade;
use App\Modules\ContentVersion\Model\ContentVersionEntity;
use App\Model\Element\ElementFacade;
use App\Model\Element\ElementEntity;
use App\Model\Version\VersionFacade;
use App\Model\Lookup\LookupFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\User;

class ContentVersionControl extends Control implements IObjectControl
{
    /** @persistent */
    public string $view = 'list';

    /** @persistent */
    public ?int $elementId = null;

    private int $componentId;
    private string $name;
    private string $code;

    private ContentVersionFacade $facade;
    private ElementFacade $elementFacade;
    private VersionFacade $versionFacade;
    private LookupFacade $lookupFacade;
    private User $user;

    public function __construct(
        ContentVersionFacade $facade,
        ElementFacade $elementFacade,
        VersionFacade $versionFacade,
        LookupFacade $lookupFacade,
        User $user
    ) {
        $this->facade = $facade;
        $this->elementFacade = $elementFacade;
        $this->versionFacade = $versionFacade;
        $this->lookupFacade = $lookupFacade;
        $this->user = $user;
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
        $this->template->name = $this->name;
        $this->template->code = $this->code;
        $this->template->componentId = $this->componentId;
        $this->template->view = $this->view;
        $this->template->states = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);

        if ($this->view === 'edit') {
            $item = $this->elementId ? $this->elementFacade->find($this->elementId) : new ElementEntity();
            $this->template->item = $item;
            $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
        } else {
            $this->template->activeElementId = $this->versionFacade->getActiveElementId($this->componentId);
            $this->template->items = $this->facade->getByComponentId($this->componentId);
            $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        }

        $this->template->render();
    }


    public function handleSetActiveVersion(int $elementId): void
    {
        $this->versionFacade->setActiveVersion($this->componentId, $elementId);
        $this->getPresenter()->flashMessage("Aktivní verze byla změněna.", 'success');
        $this->redrawControl();
    }

    public function handleEdit(?int $elementId = null): void
    {
        $this->view = 'edit';
        $this->elementId = $elementId;
        $this->redrawControl();
    }

    public function handleBack(): void
    {
        $this->view = 'list';
        $this->elementId = null;
        $this->redrawControl();
    }

    public function handleRemoveFromPage(): void
    {
        $this->getPresenter()->handleRemoveFromPage($this->componentId, (int)$this->getPresenter()->id);
    }

    public function handleDeleteFromPresentation(): void
    {
        $items = $this->facade->getByComponentId($this->componentId);
        foreach ($items as $item) {
            $this->facade->delete($item->getId());
            $this->elementFacade->delete($item->getId());
        }
        $this->getPresenter()->handleDeleteFromPresentation($this->componentId, (int)$this->getPresenter()->id);
    }

    // --- Edit Form ---

    protected function createComponentEditForm(): Form
    {
        $form = new Form();

        $form->addHidden('element_id');
        $form->addText('name', 'Název verze')
            ->setRequired('Zadejte název verze');

        // C_ELEMENT_STATUS should be defined or loaded via LookupFacade
        $statuses = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);
        $form->addSelect('status_id', 'Stav', $statuses)
            ->setRequired('Vyberte stav');

        $form->addText('valid_from', 'Platnost od')
            ->setHtmlType('date');
        $form->addText('valid_to', 'Platnost do')
            ->setHtmlType('date');

        $form->addTextArea('content', 'Obsah')
            ->setHtmlAttribute('class', 'wysiwyg')
            ->setHtmlAttribute('data-fm-url', $this->getPresenter()->link(':Admin:Files:default'));

        $form->addSubmit('save', 'Uložit verzi');

        if ($this->elementId) {
            $element = $this->elementFacade->find($this->elementId);
            $content = $this->facade->find($this->elementId);
            if ($element && $content) {
               
                $values = $element->getEntityData();
                $values['content'] = $content->getContent();

                // Format dates for HTML5 input type="date"
                if ($element->getValidFrom()) $values['valid_from'] = $element->getValidFrom('Y-m-d');
                if ($element->getValidTo()) $values['valid_to'] = $element->getValidTo('Y-m-d');

                $form->setDefaults($values);
            }
        }

        $form->onSuccess[] = [$this, 'editFormSucceeded'];
        return $form;
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

        $content = $id ? $this->facade->find($id) : new ContentVersionEntity();
        $content->setId($elementId);
        $content->setContent($values['content']);

        // If it was a new element, we must insert it because save() would try to update due to presence of ID
        if (!$id) {
            $this->facade->save($content);
        } else {
            $this->facade->save($content);
        }

        $this->getPresenter()->flashMessage('Verze obsahu byla uloĹľena.', 'success');

        $this->view = 'list';
        $this->elementId = null;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }
}

interface IContentVersionControlFactory
{
    public function create(): ContentVersionControl;
}
