<?php

namespace App\Modules\Documents\Components;

use App\Model\Admin\LoggedUserEntity;
use App\Model\Element\ElementEntity;
use App\Model\Element\ElementFacade;
use App\Model\FileManager\FileManagerFacade;
use App\Model\Helper\IObjectControl;
use App\Model\Lookup\LookupFacade;
use App\Modules\Documents\Model\DocumentsEntity;
use App\Modules\Documents\Model\DocumentsFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class DocumentsAdminControl extends Control implements IObjectControl
{
    /** @persistent */
    public string $view = 'list';

    /** @persistent */
    public ?int $elementId = null;

    private int $componentId;
    private string $name;
    private string $code;

    private DocumentsFacade $facade;
    private ElementFacade $elementFacade;
    private LookupFacade $lookupFacade;
    private FileManagerFacade $fileManagerFacade;
    public LoggedUserEntity $loggedUser;

    public function __construct(
        DocumentsFacade $facade,
        ElementFacade $elementFacade,
        LookupFacade $lookupFacade,
        FileManagerFacade $fileManagerFacade,
        LoggedUserEntity $loggedUser
    ) {
        $this->facade = $facade;
        $this->elementFacade = $elementFacade;
        $this->lookupFacade = $lookupFacade;
        $this->fileManagerFacade = $fileManagerFacade;
        $this->loggedUser = $loggedUser;
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
        $this->template->loggedUserEntity = $this->loggedUser;

        if ($this->view === 'edit') {
            $item = $this->elementId ? $this->elementFacade->find($this->elementId) : new ElementEntity();
            $this->template->item = $item;
            
            $file = null;
            if ($this->elementId) {
                $doc = $this->facade->find($this->elementId);
                if ($doc && $doc->getFileId()) {
                    $file = $this->fileManagerFacade->getFile($doc->getFileId());
                }
            }
            $this->template->selectedFile = $file;
            $this->template->setFile(__DIR__ . '/../templates/Admin/edit.latte');
        } else {
            $this->template->items = $this->facade->getByComponentId($this->componentId);
            $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        }

        $this->template->render();
    }

    public function handleCopy(int $elementId): void
    {
        $oldElement = $this->elementFacade->find($elementId);
        $oldDoc = $this->facade->find($elementId);

        if ($oldElement && $oldDoc) {
            $newElement = clone $oldElement;
            $newElement->setId(null);
            $newElement->setAuthorId($this->loggedUser->getId());
            $newElement->setInserted(new \DateTime());
            $newElement->setName($oldElement->getName() . ' (kopie)');

            $newId = $this->elementFacade->save($newElement);

            $newDoc = new DocumentsEntity();
            $newDoc->setId($newId);
            $newDoc->setText($oldDoc->getText());
            $newDoc->setFileId($oldDoc->getFileId());
            $this->facade->save($newDoc);

            $this->getPresenter()->flashMessage("Dokument byl zkopírován.", 'success');
        }
        $this->redrawControl();
    }

    public function handleDelete(int $elementId): void
    {
        $this->facade->delete($elementId);
        $this->elementFacade->delete($elementId);
        $this->getPresenter()->flashMessage("Dokument byl smazán.", 'success');
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

    protected function createComponentEditForm(): Form
    {
        $form = new Form();

        $form->addHidden('element_id');
        $form->addText('name', 'Interní název')
            ->setRequired('Zadejte interní název');

        $form->addText('text', 'Popis dokumentu')
            ->setRequired('Zadejte popis dokumentu');

        $form->addHidden('file_id')
            ->setRequired('Vyberte soubor');

        $statuses = $this->lookupFacade->getLookupListOption(C_ELEMENT_STATUS);
        $form->addSelect('status_id', 'Stav', $statuses)
            ->setRequired('Vyberte stav');

        $form->addText('valid_from', 'Platnost od')
            ->setHtmlType('date');
        $form->addText('valid_to', 'Platnost do')
            ->setHtmlType('date');

        $form->addSubmit('save', 'Uložit dokument');

        if ($this->elementId) {
            $element = $this->elementFacade->find($this->elementId);
            $doc = $this->facade->find($this->elementId);
            if ($element && $doc) {
                $values = $element->getEntityData();
                $values['text'] = $doc->getText();
                $values['file_id'] = $doc->getFileId();

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

        $elementId = $this->elementFacade->save($element, $this->loggedUser->getId());

        $doc = $id ? $this->facade->find($id) : new DocumentsEntity();
        $doc->setId($elementId);
        $doc->setText($values['text']);
        $doc->setFileId((int)$values['file_id']);

        $this->facade->save($doc);

        $this->getPresenter()->flashMessage('Dokument byl uložen.', 'success');

        $this->view = 'list';
        $this->elementId = null;

        if ($this->getPresenter()->isAjax()) {
            $this->redrawControl();
        } else {
            $this->redirect('this');
        }
    }
}

interface IDocumentsAdminControlFactory
{
    public function create(): DocumentsAdminControl;
}
