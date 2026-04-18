<?php

namespace App\FrontModule\Presenters;

use App\Model\Page\PageEntity;
use App\Model\Presentation\PresentationEntity;
use App\Model\System\FrontendRunner;
use App\Presenters\BasePresenter;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\ComponentModel\IComponent;


abstract class FrontPresenter extends BasePresenter
{

    /** @persistent */
    public int $page_id;
    public PageEntity|null $activePage = null;
    public PresentationEntity|null $activePresentation = null;

    /** @var FrontendRunner @inject */
    public FrontendRunner $frontRunner;



    public function startup(): void
    {
        $this->page_id =  (int)$this->getParameter('page_id');
        $this->frontRunner->setPresenter($this);
        $this->frontRunner->run();
        parent::startup();

    }


    public function createComponent(string $name): ?IComponent
    {

        foreach ($this->frontRunner->componentsConfig as $moduleKey => $instances) {
            foreach ($instances as $componentId => $config) {
                $expectedName = $this->frontRunner->generateComponentName($moduleKey, $componentId, $config['code_name']);

                if ($name === $expectedName) {
                    $control = $this->frontRunner->frontControlFactory->create($config['module_original'],$componentId, $config['module_original'], $config['code_name']);
                    if ($control) {
                        foreach ($config['calls'] as $call) {
                            $method = 'action' . ucfirst($call['action']);
                            if (method_exists($control, $method)) {
                                $control->$method(...array_values($call['params'] ?? []));
                            }
                        }

                        return $control;
                    }
                }
            }
        }

        return null;
    }



    public function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->activePage =  $this->activePage = $this->frontRunner->activePage;
        $this->template->active_page_id =  $this->frontRunner->active_page_id;
        $this->template->activePresentation =  $this->activePresentation = $this->frontRunner->activePresentation;
        $this->template->menuTree =  $this->frontRunner->menuTree;
        $this->template->pages =  $this->frontRunner->pages;
        $this->template->components =  $this->frontRunner->components;
        $this->template->specParamPresentation =  $this->frontRunner->specParamPresentation;
        $this->template->specParamPage =  $this->frontRunner->specParamPage;
        $this->template->webTexts = $this->frontRunner->webTexts;
        $this->template->SYS_CONST = $this->frontRunner->systemConstnants;

    }
}
