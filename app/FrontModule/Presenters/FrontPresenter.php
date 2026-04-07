<?php

namespace App\FrontModule\Presenters;

use App\Model\Page\PageEntity;
use App\Model\Page\PageFacade;
use App\Model\Presentation\PresentationEntity;
use App\Model\Presentation\PresentationFacade;
use App\Model\System\Cache;
use App\Model\Template\TemplateFacade;
use App\Presenters\BasePresenter;

abstract class FrontPresenter extends BasePresenter
{

    public ?int $active_presentation_id = null;
    public ?int $active_page_id = null;
    public PageEntity|null $activePage = null;
    public PresentationEntity|null $activePresentation = null;

    public array $specParamPresentation = [];
    public array $specParamPage = [];


    public array $menuTree = [];

    /** @var PresentationFacade @inject */
    public PresentationFacade $presentationFacade;

    /** @var PageFacade @inject */
    public PageFacade $pageFacade;

    /** @var Cache @inject */
    public Cache $cache;

    /** @var TemplateFacade @inject */
    public TemplateFacade $templateFacade;

    public function startup(): void
    {
        parent::startup();

        //todo load data
        $this->getActivePresentation();
        $this->getActivePage();
        $this->getMenuTree();
        $this->getSpecParamPresentation();
        $this->getSpecParamPage();

        $this->loadPageTemplate();
        $this->resolvePresentationComponentAction();
        $this->resolvePageComponentAction();
        $this->loadPageComponents();
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
        $domain = $this->getHttpRequest()->getUrl()->getHost();
        $cacheKey = 'active_presentation_' . $domain;

        $presentation = $this->cache->load($cacheKey, function() use ($domain) {
            $presentation = $this->presentationFacade->getPresentationByDomain($domain);

            if (!$this->checkPresentationIsEnabled($presentation)) {
                $presentation = $this->presentationFacade->getDefaultPresentation();
            }

            return $presentation;
        }, ['presentation']);

        if (!$this->checkPresentationIsEnabled($presentation)) {
            $this->error('Presentation not found or not active.', 404);
        }

        $this->activePresentation = $presentation;
        $this->active_presentation_id = $presentation->getId();
    }



    public function getActivePage() : void
    {
        $pageId = (int)$this->getParameter('id');

        if ($pageId) {
            $page = $this->pageFacade->getPageById($pageId, $this->active_presentation_id);

            if ($this->checkPageIsEnabled($page)) {

                if ($page->getPageRedirectId()) {
                     $this->redirect('this', ['id' => $page->getPageRedirectId()]);
                } elseif ($page->getPageRedirect()) {
                     $this->redirectUrl($page->getPageRedirect());
                }

                $this->activePage = $page;
                $this->active_page_id = $page->getId();
                return;
            }
        }

        $defaultPage = $this->pageFacade->getDefaultPage($this->active_presentation_id, C_PRESENTATION_STATUS_ACTIVE);

        if ($defaultPage) {
             //if found default redirect with 301 on this page
            $this->redirectPermanent('this', ['id' => $defaultPage->getId()]);
        }

        $this->error('Page not found.', 404);
    }


    public function getMenuTree() : void
    {
        $cacheKey = 'menu_tree_' . $this->active_presentation_id;

        $this->menuTree = $this->cache->load($cacheKey, function() {
            $pages = $this->pageFacade->getPages($this->active_presentation_id);
            return $this->filterActivePagesFromTree($pages);
        }, ['menu_tree', 'page']);
    }

    private function filterActivePagesFromTree(array $pages): array
    {
        $filtered = [];
        foreach ($pages as $page) {
            if ($this->checkPageIsEnabled($page) and $page->getPageMenu() == 'Y') {
                if (!empty($page->children)) {
                    $page->children = $this->filterActivePagesFromTree($page->children);
                }
                $filtered[] = $page;
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
        if (!$this->activePage || !$this->activePage->getTemplateId()) {
            $this->error('Page template not found.', 404);
        }

        $templateId = $this->activePage->getTemplateId();

        $cacheKey = 'page_template_' . $templateId;

        $templateEntity = $this->cache->load($cacheKey, function() use ($templateId) {
            return $this->templateFacade->getTemplate($templateId);
        }, ['template']);

        if (!$templateEntity || !$templateEntity->getTemplateFilename()) {
            $this->error('Page template not found.', 404);
        }

        $templateFile = __DIR__ . '/../templates/' . $templateEntity->getTemplateFilename() . '.latte';
        if (file_exists($templateFile)) {
             $this->template->setFile($templateFile);
        } else {
             $this->error('Page template file not found.', 404);
        }
    }

    public function resolvePresentationComponentAction() : void
    {
        // load presentation component action eg contentVersion via factory need prepare new factory for frontend and process action by
        // its mean call action from action and add params from params field

    }
    public function resolvePageComponentAction()
    {
        // load page_component_action eg for contentVersion, banners etc via factory need prepare new factor for all models if not exist for frontend and process action by
        // its mean call action from action and add params from params field
    }

    public function loadPageComponents()
    {
        // load page_component eg contentVersion and via frontend factory need prepare new factor for all models if not exist for frontend
        //$this->componentFactories = [];

    }

    public function processComponents()
    {
        //use $this->componentFactories
        //  process listAction for all component from loadPageComponents default listAction for this and detailAction for call detail action if has sence
        // for example news will have detailAction but banner not need detailAction so we need resolve what is called and call this action if exist if not call nothing
        //logic for edit /detail also need resolve in template... list action vs detail.. detail will have other template for example... its mean only detail on page will be..
        //easiest way is maby rewrite whole content block when detail maybe no i will specify this

    }




}
