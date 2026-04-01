<?php

namespace App\Modules\ContentVersion\Components;

use App\AdminModule\Components\IObjectControl;
use App\Modules\ContentVersion\Model\ContentVersionFacade;
use Nette\Application\UI\Control;

class ContentVersionControl extends Control implements IObjectControl
{
    private int $componentId;
    private string $name;
    private string $code;
    private ContentVersionFacade $facade;

    public function __construct(ContentVersionFacade $facade)
    {
        $this->facade = $facade;
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
        $this->template->items = $this->facade->getByComponentId($this->componentId);
        $this->template->setFile(__DIR__ . '/../templates/Admin/list.latte');
        $this->template->render();
    }

    // --- Actions ---

    public function handleToggleActive(int $id, bool $state): void
    {
        $entity = $this->facade->find($id);
        if ($entity) {
            $entity->setActive($state ? 1 : 0);
            $this->facade->save($entity);
            $this->getPresenter()->flashMessage("Stav objektu '{$entity->getName()}' byl změněn.", 'success');
        }
        $this->redrawControl();
    }

    public function handleRemoveFromPage(): void
    {
        $this->getPresenter()->handleRemoveFromPage($this->componentId, (int)$this->getPresenter()->id);
    }

    public function handleDeleteFromPresentation(): void
    {
        // 1. Module specific cleanup (content_version table)
        $items = $this->facade->getByComponentId($this->componentId);
        foreach ($items as $item) {
            $this->facade->delete($item->getId());
        }
        
        // 2. Presenter cleanup (component & page_component tables)
        $this->getPresenter()->handleDeleteFromPresentation($this->componentId, (int)$this->getPresenter()->id);
    }
}

interface IContentVersionControlFactory
{
    public function create(): ContentVersionControl;
}
