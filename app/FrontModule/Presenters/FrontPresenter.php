<?php

namespace App\FrontModule\Presenters;

use App\Model\Page\PageEntity;
use App\Model\Presentation\PresentationEntity;
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


    public function getActivePresentation() : void
    {
            //load by domain if not take default amd with presentation_status C_PRESENTATION_STATUS_ACTIVE
            //if found set activePresentation and active_presentation_id
            //if not found 404



    }


    public function getActivePage() : void
    {
        //load PageEntity by active_presentation_id and page_status  = C_PRESENTATION_STATUS_ACTIVE
        //if found  resolve redirect if found redirect else set pageEntity a activePageId
        // if not find default with page_status C_PRESENTATION_STATUS_ACTIVE pages are sort by position ASC
            //if found default  redirect with 301on this page
            //if not found 404


    }

    public function getMenuTree() : void
    {
        //load menu by active_presentation_id from active pages only...
        //store to cache
        //if page updated or add new in admin invalidate cache we have event manager for this

    }


    public function getSpecParamPresentation() : void
    {
        //load specParamPresetation data by active_presentation_id
        //store to cache
        //if specParamPresetation updated or add new in admin invalidate cache we have event manager for this
    }

    public function getSpecParamPage() : void
    {
        //load specParamPage data by active_presentation_id
        //store / load  to cache and store to specParamPage
        //if specParamPage updated or add new in admin invalidate cache we have event manager for this
    }

    public function loadPageTemplate() : void
    {
        //assign template by activePage template if not found  404
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
