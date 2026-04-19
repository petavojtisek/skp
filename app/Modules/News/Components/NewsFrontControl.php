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

    /** @var int|null @persistent */
    public int $offset;

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

        $spec_params = $this->getPresenter()->frontRunner->specParamPage;
        $spec_params_pres = $this->getPresenter()->frontRunner->specParamPresentation;

        $limit = $spec_params_pres['news_limit']?? $spec_params['news_limit']?? 5;
        $offset = $this->offset ?? 0;
        $is_hp = $spec_params['is_homepage']??0;

        $list = $this->facade->getFrontByComponentId($this->componentId, $limit, $offset);
        $this->template->items = $list;
        if($is_hp) {
            $this->template->setFile(__DIR__ . '/../templates/Front/hp.latte');
        }else{
            $this->template->setFile(__DIR__ . '/../templates/Front/list.latte');
        }
        $this->template->render();
    }

    public function renderDetail()
    {
        xdebug_break();
    }

    public function actionDefault(...$params): void
    {
    }
}

interface INewsFrontControlFactory
{
    public function create(): NewsFrontControl;
}
