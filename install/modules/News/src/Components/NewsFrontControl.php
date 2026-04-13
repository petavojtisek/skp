<?php

namespace App\Modules\News\Components;

use App\Model\Helper\IObjectControl;
use App\Modules\News\Model\NewsFacade;
use Nette\Application\UI\Control;

class NewsFrontControl extends Control implements IObjectControl
{
    private NewsFacade $facade;
    private int $componentId;
    private string $name;
    private string $code;

    public function __construct(NewsFacade $facade)
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
        $this->template->items = $this->facade->getFrontByComponentId($this->componentId);
        $this->template->setFile(__DIR__ . '/../templates/Front/list.latte');
        $this->template->render();
    }

    public function actionDefault(...$params): void
    {
    }
}

interface INewsFrontControlFactory
{
    public function create(): NewsFrontControl;
}
