<?php

namespace App\Modules\ContentVersion\Components;

use App\Model\Element\ElementFacade;
use App\Model\Helper\IObjectControl;
use App\Modules\ContentVersion\Model\ContentVersionFacade;
use App\Model\Version\VersionFacade;
use Nette\Application\UI\Control;

class ContentVersionFrontControl extends Control implements IObjectControl
{
    private ContentVersionFacade $facade;
    private VersionFacade $versionFacade;
    private ElementFacade $elementFacade;
    private int $componentId;
    private string $name;
    private string $code;



    public function __construct(
        ContentVersionFacade $facade,
        VersionFacade $versionFacade,
        ElementFacade $elementFacade
    ) {
        $this->facade = $facade;
        $this->versionFacade = $versionFacade;
        $this->elementFacade = $elementFacade;
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
        $this->template->content = '';
        $activeElementId = $this->versionFacade->getActiveElementId($this->componentId);
        if ($activeElementId) {
            $element = $this->elementFacade->findFront($activeElementId);
            if($element) {
                $content = $this->facade->find($activeElementId);
                $this->template->content = $content ? $content->getContent() : '';
            }
        }

        $this->template->setFile(__DIR__ . '/../templates/Front/list.latte');
        $this->template->render();
    }

    /**
     * Generic action handler called from presenter
     */
    public function actionDefault(...$params): void
    {
        // For ContentVersion, default action might not need special logic
    }
}

interface IContentVersionFrontControlFactory
{
    public function create(): ContentVersionFrontControl;
}

