<?php

namespace App\Modules\ContentVersion\Components;

use App\Modules\ContentVersion\Model\ContentVersionFacade;
use App\Model\Version\VersionFacade;
use Nette\Application\UI\Control;

class ContentVersionFrontControl extends Control
{
    private ContentVersionFacade $facade;
    private VersionFacade $versionFacade;
    private int $componentId;

    public function __construct(
        ContentVersionFacade $facade,
        VersionFacade $versionFacade
    ) {
        $this->facade = $facade;
        $this->versionFacade = $versionFacade;
    }

    public function setComponentId(int $componentId): void
    {
        $this->componentId = $componentId;
    }

    public function render(): void
    {
        $activeElementId = $this->versionFacade->getActiveElementId($this->componentId);
        if ($activeElementId) {
            $content = $this->facade->find($activeElementId);
            $this->template->content = $content ? $content->getContent() : '';
        } else {
            $this->template->content = '';
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
