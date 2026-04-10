<?php

namespace App\Model\System;

use App\FrontModule\Presenters\FrontPresenter;
use App\Model\Page\PageEntity;
use App\Model\Page\PageFacade;
use App\Model\Presentation\PresentationEntity;
use App\Model\Presentation\PresentationFacade;
use App\Model\System\Cache;
use App\Model\Template\TemplateFacade;
use App\Model\Helper\FrontControlFactory;
use App\Model\Component\ComponentFacade;
use App\Modules\WebTexts\Model\WebTextFacade;
use Nette\ComponentModel\IComponent;
use Nette\Http\IRequest;


class FrontendRunner
{



    /** @var PresentationFacade @inject */
    public PresentationFacade $presentationFacade;

    /** @var PageFacade @inject */
    public PageFacade $pageFacade;

    /** @var Cache @inject */
    public Cache $cache;

    /** @var TemplateFacade @inject */
    public TemplateFacade $templateFacade;

    /** @var FrontControlFactory @inject */
    public FrontControlFactory $frontControlFactory;

    /** @var ComponentFacade @inject */
    public ComponentFacade $componentFacade;

    /** @var WebTextFacade @inject */
    public WebTextFacade $webTextFacade;

    public IRequest $httpRequest;
    public FrontPresenter $presenter;

    public PageEntity|null $activePage = null;
    public PresentationEntity|null $activePresentation = null;
    public int $active_presentation_id ;
    public int $active_page_id ;
    public array $componentsConfig = [];
    public array $components = [];
    public array $specParamPresentation = [];
    public array $specParamPage = [];

    public array $menuTree = [];
    public array $pages = [];
    public array $webTexts = [];


    public function __construct(PresentationFacade $presentationFacade,
        PageFacade $pageFacade,
        Cache $cache,
        TemplateFacade $templateFacade,
        FrontControlFactory $frontControlFactory,
        ComponentFacade $componentFacade,
        WebTextFacade $webTextFacade,
        IRequest $httpRequest
    )
    {


        $this->presentationFacade = $presentationFacade;
        $this->pageFacade = $pageFacade;
        $this->cache = $cache;
        $this->templateFacade = $templateFacade;
        $this->frontControlFactory = $frontControlFactory;
        $this->componentFacade = $componentFacade;
        $this->webTextFacade = $webTextFacade;
        $this->httpRequest = $httpRequest;
    }

    public function setPresenter(FrontPresenter $presenter): void
    {
        $this->presenter = $presenter;
    }


    public function run(): void
    {
        $this->getActivePresentation();
        $this->getActivePage();
        $this->getMenuTree();
        $this->getPages();
        $this->getSpecParamPresentation();
        $this->getSpecParamPage();
        $this->loadPageTemplate();
        $this->loadWebTexts();

        // 1. Collect all requested actions
        $this->resolvePresentationComponentAction();
        $this->resolvePageComponentAction();
        $this->loadPageComponents();

        // 2. Actually process/trigger them
        $this->processComponents();
    }

    private function checkPageIsEnabled($page) : bool
    {
        return ($page instanceof PageEntity and $page->getPageStatus() == C_PRESENTATION_STATUS_ACTIVE);
    }


    private function checkPresentationIsEnabled($presentation) : bool
    {
        return ($presentation instanceof PresentationEntity and $presentation->getPresentationStatus() == C_PRESENTATION_STATUS_ACTIVE);
    }


    public function getActivePresentation() : void
    {
        $domain = $this->httpRequest->getUrl()->getHost();
        $cacheKey = 'active_presentation_' . $domain;

        $presentation = $this->cache->load($cacheKey, function() use ($domain) {
            $presentation = $this->presentationFacade->getPresentationByDomain($domain);

            if (!$this->checkPresentationIsEnabled($presentation)) {
                $presentation = $this->presentationFacade->getDefaultPresentation();
            }

            return $presentation;
        }, ['presentation']);

        if (!$this->checkPresentationIsEnabled($presentation)) {
            $this->presenter->error('Presentation not found or not active.', 404);
        }

        $this->activePresentation = $presentation;
        $this->active_presentation_id = $presentation->getId();
    }



    public function getActivePage() : void
    {
        $pageId = (int)$this->presenter->getParameter('page_id');

        if ($pageId) {
            $page = $this->pageFacade->getPageById($pageId, $this->active_presentation_id);
        }

        if ($page && $page->getPageStatus() == C_PRESENTATION_STATUS_ACTIVE) {
            // Internal or External Redirects defined in DB
            if ($page->getPageRedirectId()) {
                $this->presenter->redirectPermanent('this', ['page_id' => $page->getPageRedirectId()]);
            } elseif ($page->getPageRedirect()) {
                $this->presenter->redirectUrl($page->getPageRedirect());
            }

            // SEO Enforcement: Redirect to canonical URL if accessed via non-rewrite URL
            if ($page->getPageRewrite()) {
                $path = ltrim($this->httpRequest->getUrl()->getPathInfo(), '/');
                $rewriteWithExtension = $page->getPageRewrite() . '.html';

                if ($path !== $page->getPageRewrite() && $path !== $rewriteWithExtension && $path !== '') {
                    $this->presenter->redirectPermanent('this', ['page_id' => $page->getId()]);
                }
            }

            $this->activePage = $page;
            $this->active_page_id = $page->getId();
            return;
        }

        $this->presenter->error('Stránka nebyla nalezena.', 404);
    }


    public function getMenuTree() : void
    {
        $cacheKey = 'menu_tree_' . $this->active_presentation_id;

        $this->menuTree = $this->cache->load($cacheKey, function() {
            $pages = $this->pageFacade->getPages($this->active_presentation_id);
            return $this->filterActivePagesFromTree($pages);
        }, ['menu_tree', 'page']);
    }

    public function getPages()
    {
        $cacheKey = 'presentation_pages_' . $this->active_presentation_id;

        $this->pages = $this->cache->load($cacheKey, function() {
            $pages = $this->pageFacade->getPages($this->active_presentation_id);
            return $this->filterActivePages($pages);
        }, ['menu_tree', 'page']);
    }

    private function filterActivePagesFromTree(array $pages): array
    {
        $filtered = [];
        foreach ($pages as $page) {
            if ($this->checkPageIsEnabled($page) and $page->getPageMenu() == 'Y') {
                $p = clone $page;
                if (!empty($p->children)) {
                    $p->children = $this->filterActivePagesFromTree($p->children);
                }
                $filtered[] = $p;
            }
        }
        return $filtered;
    }

    private function filterActivePages(array $pages): array
    {
        $filtered = [];
        foreach ($pages as $page) {
            if ($this->checkPageIsEnabled($page)) {
                $p = clone $page;
                if (!empty($p->children)) {
                    $p->children = $this->filterActivePages($p->children);
                }
                $filtered[] = $p;
            }
        }
        return $filtered;
    }


    public function getSpecParamPresentation() : void
    {
        $cacheKey = 'spec_param_presentation_' . $this->active_presentation_id;

        $this->specParamPresentation = $this->cache->load($cacheKey, function () {
            $params = $this->presentationFacade->getSpecParams($this->active_presentation_id);
            $result = [];
            foreach ($params as $param) {
                $result[$param->getName()] = $param->getValue();
            }
            return $result;
        }, ['spec_param_presentation']);
    }

    public function getSpecParamPage() : void
    {
        if (!$this->active_page_id) {
            return;
        }

        $cacheKey = 'spec_param_page_' . $this->active_page_id;

        $this->specParamPage = $this->cache->load($cacheKey, function () {
            $params = $this->pageFacade->getSpecParams($this->active_page_id);
            $result = [];
            foreach ($params as $param) {
                $result[$param->getName()] = $param->getValue();
            }
            return $result;
        }, ['spec_param_page']);
    }

    public function loadPageTemplate() : void
    {

        if (!$this->activePage or !$this->activePage->getTemplateId()) {
            $this->presenter->error('Page template not found.', 404);
        }

        $templateId = $this->activePage->getTemplateId();
        $cacheKey = 'page_template_' . $templateId;

        $templateEntity = $this->cache->load($cacheKey, function() use ($templateId) {
            return $this->templateFacade->getTemplate($templateId);
        }, ['template']);

        if (!$templateEntity or !$templateEntity->getTemplateFilename()) {
            $this->presenter->error('Page template not found.', 404);
        }

        $templateFile = PROJECT_ROOT_DIR . DS .'app/FrontModule/templates/' . $templateEntity->getTemplateFilename() ;
        if (file_exists($templateFile)) {
            $this->presenter->template->setFile($templateFile);
        } else {
            $this->presenter->error('Page template file not found.', 404);
        }
    }

    public function loadWebTexts(): void
    {
        $cacheKey = 'all_web_texts';

        $this->webTexts = $this->cache->load($cacheKey, function() {
            $texts = $this->webTextFacade->getAllWebTexts();
            $result = [];
            foreach ($texts as $text) {
                $result[$text->getCode()] = $text->getText();
            }
            return $result;
        }, ['web_text']);
    }

    public function generateComponentName(string $module, int $componentId, ?string $codeName): string
    {
        $moduleSnake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $module));
        $codeNameSnake = $codeName ? strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $codeName)) : null;
        return $codeNameSnake ? "{$moduleSnake}_{$codeNameSnake}" : "{$moduleSnake}_{$componentId}";
    }

    public function addComponentCall(int $componentId, string $module, string $action, array $params = [], ?string $codeName = null): void
    {
        // Normalize module name for key (PascalCase) to ensure consistent grouping
        $moduleKey = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $module)));

        if (!isset($this->componentsConfig[$moduleKey])) {
            $this->componentsConfig[$moduleKey] = [];
        }

        if (!isset($this->componentsConfig[$moduleKey][$componentId])) {
            $this->componentsConfig[$moduleKey][$componentId] = [
                'module_original' => $moduleKey, // Original name for factory
                'code_name' => $codeName,
                'calls' => []
            ];

            $this->components[$moduleKey][$codeName] = 1;
        }




        // Update code_name if we found it later (e.g. from physical component load)
        if ($codeName !== null && $this->componentsConfig[$moduleKey][$componentId]['code_name'] === null) {
            $this->componentsConfig[$moduleKey][$componentId]['code_name'] = $codeName;
            $this->components[$moduleKey][$codeName] = 1;
        }

        $this->componentsConfig[$moduleKey][$componentId]['calls'][] = [
            'action' => $action,
            'params' => $params
        ];
    }

    public function resolvePresentationComponentAction() : void
    {
        $actions = $this->presentationFacade->getComponentActions($this->active_presentation_id);
        foreach ($actions as $actionRow) {
            // Use entity methods if it's an entity, or array access
            if ($actionRow instanceof \App\Model\Presentation\ComponentActionEntity) {
                $params = $actionRow->getParams() ?: [];
                $this->addComponentCall((int)$actionRow->getComponentId(), $actionRow->getModule(), $actionRow->getAction(), $params);
            } else {
                $params = isset($actionRow->params) ? json_decode($actionRow->params, true) : [];
                $this->addComponentCall((int)$actionRow->component_id, $actionRow->module, $actionRow->action, $params);
            }
        }
    }

    public function resolvePageComponentAction(): void
    {
        if (!$this->active_page_id) return;
        $actions = $this->pageFacade->getComponentActions($this->active_page_id);
        foreach ($actions as $actionRow) {
            if (is_array($actionRow)) {
                $params = isset($actionRow['param']) ? json_decode($actionRow['param'], true) : [];
                $this->addComponentCall((int)$actionRow['component_id'], $actionRow['module'], $actionRow['action'], $params);
            } else {
                $params = method_exists($actionRow, 'getParams') ? $actionRow->getParams() : (isset($actionRow->param) ? json_decode($actionRow->param, true) : []);
                $this->addComponentCall((int)$actionRow->getComponentId(), $actionRow->getModule(), $actionRow->getAction(), $params);
            }
        }
    }

    public function loadPageComponents(): void
    {
        if (!$this->active_page_id) return;

        $components = $this->componentFacade->getByPageId($this->active_page_id);
        foreach ($components as $component) {
            // Physical components always get 'default' action unless already specified
            $this->addComponentCall($component->getId(), $component->getModuleCodeName(), 'default', [], $component->getCodeName());
        }
    }

    public function processComponents(): void
    {
        foreach ($this->componentsConfig as $moduleKey => $instances) {
            foreach ($instances as $componentId => $config) {
                $componentName = $this->generateComponentName($moduleKey, $componentId, $config['code_name']);
                $this->presenter->getComponent($componentName);
            }
        }
    }


}
